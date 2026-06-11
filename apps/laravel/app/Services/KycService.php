<?php

namespace App\Services;

class KycService
{
    private const ALLOWED_DOCUMENT_TYPES = ['passport', 'national_id', 'residence_permit'];
    private const SANCTIONED_COUNTRIES   = ['KP', 'IR', 'SY', 'CU', 'SD'];
    private const HIGH_RISK_COUNTRIES    = ['NG', 'MM', 'BY', 'RU', 'AF'];

    public function validate_identity_document(array $document): bool
    {
        if (empty($document['number'])) {
            return false;
        }

        if (!in_array($document['type'], self::ALLOWED_DOCUMENT_TYPES, true)) {
            return false;
        }
        $expiresAt = new \DateTime($document['expires_at']);
        $today     = new \DateTime();

        if ($expiresAt < $today) {
             throw new \InvalidArgumentException('Document expired');
        }

        return true;
    }

    public function compute_kyc_risk_level(array $context): string
    {
        $country       = strtoupper($context['country']);
        $accountMonths = $context['account_age_months'];
        $monthlyAvg    = $context['monthly_average'];

        if (in_array($country, self::HIGH_RISK_COUNTRIES, true) || $monthlyAvg > 20000.00) {
            return 'HIGH';
        }

        if ($accountMonths < 6 || ($monthlyAvg >= 5000.00 && $monthlyAvg <= 20000.00)) {
           return 'MEDIUM';
        }

        return 'LOW';
    }

    public function evaluate_kyc(array $data): array
    {
        $country = strtoupper($data['country']);
        if (in_array($country, self::SANCTIONED_COUNTRIES, true)) {
            return [
                'status' => 'REJECTED',
                'reason' => 'SANCTIONED_COUNTRY',
            ];
        }
        
        try {
            $documentValid = $this->validate_identity_document($data['document']);
        } catch (\InvalidArgumentException $e) {
            return [
                'status' => 'REJECTED',
                'reason' => 'EXPIRED_DOCUMENT',
            ];
        }

        if (!$documentValid) {
            return [
                'status' => 'REJECTED',
                'reason' => 'INVALID_DOCUMENT',
            ];
        }

        $riskLevel = $this->compute_kyc_risk_level([
            'country'            => $country,
            'account_age_months' => $data['account_age_months'],
            'monthly_average'    => $data['monthly_average'],
        ]);

        if ($riskLevel === 'HIGH') {
            return [
                'status'     => 'APPROVED',
                'risk_level' => $riskLevel,
            ];
        }

        return [
            'status'     => 'APPROVED',
            'risk_level' => $riskLevel,
        ];
    }
}
