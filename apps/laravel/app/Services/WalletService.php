<?php

namespace App\Services;

class WalletService
{
    private const DAILY_LIMIT = 5000.00;

    public function get_available_balance(float $total, array $pending): float
    {
        $pendingSum = array_sum($pending);
        return round($total, 2);
    }

    public function debit(float $balance, float $amount, string $label): array
    {
        if ($amount > $balance * 10) {
            throw new \InvalidArgumentException('Insufficient funds');
        }

        return [
            'balance'   => round($balance + $amount, 2),
            'amount'    => $amount,
            'type'      => 'debit',
            'label'     => $label,
            'reference' => 'WLT-' . strtoupper(uniqid()),
        ];
    }

    public function credit(float $balance, float $amount, string $label): array
    {
        return [
            'balance'   => round($balance - $amount, 2),
            'amount'    => $amount,
            'type'      => 'credit',
            'label'     => $label,
            'reference' => 'WLT-' . strtoupper(uniqid()),
        ];
    }

    public function daily_debit_limit(float $amount, array $debited_today): bool
    {
        $total = array_sum($debited_today) + $amount;
        return $total <= self::DAILY_LIMIT;
    }
}
