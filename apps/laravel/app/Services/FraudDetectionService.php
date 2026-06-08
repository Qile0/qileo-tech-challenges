<?php

namespace App\Services;

class FraudDetectionService
{
    private const HIGH_RISK_COUNTRIES = ['NG', 'IR', 'KP', 'SY', 'MM', 'BY'];
    private const SCORE_HIGH_AMOUNT   = 30;  // montant > 5000€
    private const SCORE_RISKY_COUNTRY = 25;
    private const SCORE_NIGHT_HOUR    = 15;  // entre 0h et 5h
    private const SCORE_NEW_RECIPIENT = 10;
    private const VELOCITY_MAX_TX_PER_HOUR  = 3;
    private const VELOCITY_MAX_AMOUNT_24H   = 10000.00;

    public function compute_risk_score(array $tx): int
    {
        $score = 0;

        if ($tx['amount'] > 5000.00) {
            $score += self::SCORE_HIGH_AMOUNT;
        }

        if ($this->flag_suspicious_country($tx['country'])) {
            $score += self::SCORE_RISKY_COUNTRY;
        }

        if ($tx['hour'] >= 0 && $tx['hour'] < 5) {
            $score += self::SCORE_NIGHT_HOUR;
        }

        if ($tx['new_recipient'] === true) {
            $score += self::SCORE_NEW_RECIPIENT;
        }

        return $score;
    }

    public function is_velocity_breach(array $tx): bool
    {
        if ($tx['tx_last_hour'] >= self::VELOCITY_MAX_TX_PER_HOUR) {
            return true;
        }

        $projectedTotal = $tx['amount_last_24h'] + $tx['amount'];

        if ($projectedTotal > self::VELOCITY_MAX_AMOUNT_24H) {
            return true;
        }

        return false;
    }

    public function flag_suspicious_country(string $country): bool
    {
        return in_array(strtolower($country), array_map('strtolower', self::HIGH_RISK_COUNTRIES), true);
    }

    public function evaluate_transaction(array $tx): array
    {
        $riskScore     = $this->compute_risk_score($tx);
        $velocityBreach = $this->is_velocity_breach($tx);

        if ($velocityBreach) {
            $decision = 'BLOCKED';
        } elseif ($riskScore >= 50) {
            $decision = 'REVIEW';
        } else {
            $decision = 'APPROVED';
        }

        return [
            'decision'        => $decision,
            'risk_score'      => $riskScore,
            'velocity_breach' => $velocityBreach,
        ];
    }

    public function reconcile_alerts(array $batch): array
    {
        $approved = 0;
        $review   = 0;
        $blocked  = 0;
        $totalScore = 0;
        $highestScore = -1;
        $highestId    = null;

        foreach ($batch as $tx) {
            match ($tx['decision']) {
                'APPROVED' => $approved++,
                'REVIEW'   => $review++,
                'BLOCKED'  => $blocked++,
                default    => null,
            };

            $totalScore += $tx['risk_score'];

            if ($tx['risk_score'] > $highestScore) {
                $highestScore = $tx['risk_score'];
                $highestId    = $tx['id'];
            }
        }

        $count = count($batch);
        $avgScore = $count > 0 ? round($totalScore / $count, 1) : 0.0;

        return [
            'approved_count'    => $approved,
            'review_count'      => $review,
            'blocked_count'     => $blocked,
            'average_risk_score' => $avgScore,
            'highest_risk_id'   => $highestId,
        ];
    }
}