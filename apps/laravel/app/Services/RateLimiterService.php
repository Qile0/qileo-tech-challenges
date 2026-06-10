<?php

namespace App\Services;

class RateLimiterService
{
    private const MAX_TRANSACTIONS = 5;
    private const WINDOW_SECONDS   = 60;

    public function allows_transaction_within_rate_limit(
        string $accountId,
        array $timestamps,
        int $now
    ): bool {
        $valid = $this->filterValidTimestamps($timestamps, $now);
        return count($valid) < self::MAX_TRANSACTIONS;
    }

    public function computes_remaining_allowance(
        array $timestamps,
        int $now
    ): int {
        $valid = $this->filterValidTimestamps($timestamps, $now);
        $remaining = self::MAX_TRANSACTIONS - count($valid);
        return max(0, $remaining);
    }

    private function filterValidTimestamps(array $timestamps, int $now): array
    {
        return array_filter(
            $timestamps,
            fn(int $ts) => ($now - $ts) <= self::WINDOW_SECONDS
        );
    }
}
