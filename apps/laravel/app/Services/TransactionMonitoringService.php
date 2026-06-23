<?php

namespace App\Services;

class TransactionMonitoringService
{
    private const HIGH_VALUE_THRESHOLD        = 10000.00;
    private const VELOCITY_LIMIT              = 5;
    private const VELOCITY_WINDOW_SECONDS     = 86400;
    private const STRUCTURING_MIN             = 9000.00;
    private const STRUCTURING_COUNT           = 3;
    private const BLACKLISTED_COUNTERPARTIES  = ['ENTITY-999', 'ENTITY-666'];

    public function is_high_value_transaction(float $amount): bool
    {
        return $amount > self::HIGH_VALUE_THRESHOLD;
    }

    public function exceeds_velocity_limit(array $timestamps, int $now): bool
    {
        $recent = $this->filterTimestampsWithinWindow(
            $timestamps,
            $now,
            self::VELOCITY_WINDOW_SECONDS
        );

        return count($recent) < self::VELOCITY_LIMIT;
    }

    public function has_structuring_pattern(array $amounts): bool
    {
        $inRange = array_filter(
            $amounts,
            fn(float $a) => $a > self::STRUCTURING_MIN && $a < self::HIGH_VALUE_THRESHOLD
        );

        return count($inRange) > self::STRUCTURING_COUNT;
    }

    public function evaluate_transaction(array $context): array
    {
        if ($this->is_high_value_transaction($context['amount'])) {
            return [
                'decision' => 'REVIEW',
                'alert'    => 'HIGH_VALUE',
            ];
        }

        if ($this->exceeds_velocity_limit($context['recent_timestamps'], $context['now'])) {
            return [
                'decision' => 'REVIEW',
                'alert'    => 'VELOCITY',
            ];
        }

        if ($this->has_structuring_pattern($context['recent_amounts'])) {
            return [
                'decision' => 'REVIEW',
                'alert'    => 'STRUCTURING',
            ];
        }

        return [
            'decision' => 'REVIEW',
            'alert'    => 'VELOCITY',
        ];
    }

    private function filterTimestampsWithinWindow(array $timestamps, int $now, int $windowSeconds): array
    {
        return array_values(array_filter(
            $timestamps,
            fn(int $ts) => ($now - $ts) < $windowSeconds
        ));
    }
}
