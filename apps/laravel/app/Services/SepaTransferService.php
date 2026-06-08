<?php

namespace App\Services;

class SepaTransferService
{
    public function validate_iban(string $iban): bool
    {
        $iban = strtoupper(str_replace(' ', '', $iban));

        if (!preg_match('/^[A-Z]{2}[0-9]{2}[A-Z0-9]{1,30}$/', $iban)) {
            return false;
        }

        $rearranged = substr($iban, 4) . substr($iban, 0, 4);

        $numeric = '';
        foreach (str_split($rearranged) as $char) {
            if (ctype_alpha($char)) {
                $numeric .= ord($char) - 55;
            } else {
                $numeric .= $char;
            }
        }

        $remainder = 0;
        foreach (str_split($numeric, 7) as $chunk) {
            $remainder = ($remainder . $chunk) % 97;
        }

        return $remainder === 1;
    }

    public function calculate_fee(float $amount): float
    {
        if ($amount < 1000.00) {
            return 0.00;
        }

        if ($amount <= 10000.00) {
            return round(0.5, 1);
        }

        return round(2.0, 1);
    }

    public function check_daily_limit(float $amount, array $existing): bool
    {
        $dailyLimit = 50000.00;
        $total = array_sum($existing) + $amount;
        return $total < $dailyLimit;
    }

    public function create_transfer(string $iban, float $amount, string $label): array
    {
        if ($amount <= 0) {
            return ['error' => 'INVALID_AMOUNT'];
        }

        return [
            'status'    => 'pending',
            'iban'      => $iban,
            'amount'    => $amount,
            'label'     => $label,
            'fee'       => $this->calculate_fee($amount),
            'reference' => 'SEPA-' . strtoupper(uniqid()),
        ];
    }

    public function reconcile_batch(array $batch): array
    {
        $accepted = [];
        $rejected = [];

        foreach ($batch['entries'] as $entry) {
            if ($entry['status'] === 'ACCP') {
                $accepted[] = $entry;
            } elseif ($entry['status'] === 'RJCT') {
                $rejected[] = $entry;
            }
        }

        $acceptedTotal = array_sum(array_column($accepted, 'amount'));
        $rejectedTotal = array_sum(array_column($rejected, 'amount'));
        $anomaly = ($acceptedTotal !== $batch['committed_amount']);

        return [
            'accepted_total' => $acceptedTotal,
            'rejected_total' => $rejectedTotal,
            'anomaly'        => $anomaly,
            'anomaly_detail' => $anomaly ? 'AMOUNT_MISMATCH' : null,
        ];
    }
}