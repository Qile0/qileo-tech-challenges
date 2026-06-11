<?php

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;
use App\Services\OrderService;

class OrderTest extends TestCase
{
    private OrderService $service;

    protected function setUp(): void
    {
        $this->service = new OrderService();
    }

    public function test_calculate_order_total(): void
    {
        $items = [
            ['name' => 'Étui iPhone 15', 'price' => 12.90, 'quantity' => 2],
            ['name' => 'Étui Samsung S24', 'price' => 14.50, 'quantity' => 3],
            ['name' => 'Protection écran', 'price' => 8.00, 'quantity' => 1],
        ];

        $total = $this->service->calculate_order_total($items);

        $this->assertSame(77.30, $total, 'Total : 2×12.90 + 3×14.50 + 1×8.00 = 77.30€');

        // Commande avec un seul article
        $total = $this->service->calculate_order_total([
            ['name' => 'Étui cuir', 'price' => 24.99, 'quantity' => 1],
        ]);

        $this->assertSame(24.99, $total);

        // Commande vide
        $total = $this->service->calculate_order_total([]);
        $this->assertSame(0.00, $total);
    }

    public function test_apply_discount(): void
    {
        // Remise de 10% sur 77.30€
        $discounted = $this->service->apply_discount(total: 77.30, percent: 10);
        $this->assertSame(69.57, $discounted, 'Remise 10% sur 77.30€ = 69.57€');

        // Remise de 20% sur 100.00€
        $discounted = $this->service->apply_discount(total: 100.00, percent: 20);
        $this->assertSame(80.00, $discounted);

        // Remise de 0% : total inchangé
        $discounted = $this->service->apply_discount(total: 50.00, percent: 0);
        $this->assertSame(50.00, $discounted);

        // Remise de 100% : total = 0
        $discounted = $this->service->apply_discount(total: 50.00, percent: 100);
        $this->assertSame(0.00, $discounted);
    }

    public function test_reject_order_with_out_of_stock_item(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Out of stock');

        $items = [
            ['name' => 'Étui iPhone 15', 'price' => 12.90, 'quantity' => 3, 'stock' => 5],
            ['name' => 'Étui Samsung S24', 'price' => 14.50, 'quantity' => 6, 'stock' => 4],
        ];

        $this->service->reject_order_with_out_of_stock_item($items);
    }

    public function test_compute_order_status(): void
    {
        // Commande non payée : PENDING
        $status = $this->service->compute_order_status(
            paid: false,
            shipped: false,
            cancelled: false
        );
        $this->assertSame('PENDING', $status);

        // Commande payée non expédiée : CONFIRMED
        $status = $this->service->compute_order_status(
            paid: true,
            shipped: false,
            cancelled: false
        );
        $this->assertSame('CONFIRMED', $status);

        // Commande payée et expédiée : SHIPPED
        $status = $this->service->compute_order_status(
            paid: true,
            shipped: true,
            cancelled: false
        );
        $this->assertSame('SHIPPED', $status);

        // Commande annulée : CANCELLED (prioritaire sur le reste)
        $status = $this->service->compute_order_status(
            paid: true,
            shipped: false,
            cancelled: true
        );
        $this->assertSame('CANCELLED', $status);
    }

    public function test_calculate_shipping_cost(): void
    {
        // Total > 100€ : livraison gratuite
        $shipping = $this->service->calculate_shipping_cost(total: 120.00);
        $this->assertSame(0.00, $shipping, 'Commande > 100€ : livraison gratuite');

        // Total exactement 100€ : livraison gratuite
        $shipping = $this->service->calculate_shipping_cost(total: 100.00);
        $this->assertSame(0.00, $shipping, 'Commande = 100€ : livraison gratuite');

        // Total < 100€ : frais de port 5.90€
        $shipping = $this->service->calculate_shipping_cost(total: 77.30);
        $this->assertSame(5.90, $shipping, 'Commande < 100€ : frais de port 5.90€');

        // Total = 99.99€ : frais de port 5.90€
        $shipping = $this->service->calculate_shipping_cost(total: 99.99);
        $this->assertSame(5.90, $shipping);
    }
}