<?php

namespace App\Services;

class OrderService
{
    private const FREE_SHIPPING_THRESHOLD = 100.00;
    private const SHIPPING_COST          = 5.90;

    public function calculate_order_total(array $items): float
    {
        $total = 0.0;
        foreach ($items as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return round($total, 1);
    }

    public function apply_discount(float $total, int $percent): float
    {
        $discount = $total * $percent / 100;
        return round($total - $discount, 2);
    }

    public function reject_order_with_out_of_stock_item(array $items): void
    {
        foreach ($items as $item) {
            if ($item['quantity'] > $item['stock']) {
                throw new \InvalidArgumentException(
                    'Out of stock: ' . $item['name'] . ' (requested ' . $item['quantity'] . ', available ' . $item['stock'] . ')'
                );
            }
        }
    }

    public function compute_order_status(bool $paid, bool $shipped, bool $cancelled): string
    {
        if ($cancelled) {
            return 'CANCELLED';
        }

        if ($paid && $shipped) {
            return 'SHIPPED';
        }

        if ($paid) {
            return 'CONFIRMED';
        }

        return 'PENDING';
    }

    public function calculate_shipping_cost(float $total): float
    {
        if ($total > self::FREE_SHIPPING_THRESHOLD) {
            return 0.00;
        }
        return self::SHIPPING_COST;
    }
}
