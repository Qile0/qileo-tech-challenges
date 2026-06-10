<?php

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;
use App\Services\RateLimiterService;

class RateLimiterTest extends TestCase
{
    private RateLimiterService $service;

    protected function setUp(): void
    {
        $this->service = new RateLimiterService();
    }

    public function test_allows_transaction_within_rate_limit(): void
    {
        $now = time();

        // 4 transactions dans la fenêtre, seuil à 5 : la 5e doit être autorisée
        $result = $this->service->allows_transaction_within_rate_limit(
            accountId: 'ACC-001',
            timestamps: [$now - 10, $now - 20, $now - 30, $now - 40],
            now: $now
        );

        $this->assertTrue($result, '4 transactions dans la fenêtre : la 5e doit être autorisée');

        // Aucune transaction : autorisé
        $result = $this->service->allows_transaction_within_rate_limit(
            accountId: 'ACC-001',
            timestamps: [],
            now: $now
        );

        $this->assertTrue($result, 'Aucune transaction dans la fenêtre : autorisé');
    }

    public function test_blocks_transaction_exceeding_rate_limit(): void
    {
        $now = time();

        // 5 transactions dans la fenêtre, seuil à 5 : la 6e doit être bloquée
        $result = $this->service->allows_transaction_within_rate_limit(
            accountId: 'ACC-001',
            timestamps: [$now - 5, $now - 15, $now - 25, $now - 35, $now - 45],
            now: $now
        );

        $this->assertFalse($result, '5 transactions dans la fenêtre : la 6e doit être bloquée');
    }

    public function test_sliding_window_expires_old_transactions(): void
    {
        $now = time();

        // 5 transactions dont 2 hors de la fenêtre (> 60s) : seules 3 comptent, autorisé
        $result = $this->service->allows_transaction_within_rate_limit(
            accountId: 'ACC-001',
            timestamps: [
                $now - 90,  // expirée
                $now - 75,  // expirée
                $now - 50,  // valide
                $now - 30,  // valide
                $now - 10,  // valide
            ],
            now: $now
        );

        $this->assertTrue($result, '2 transactions expirées sur 5 : seulement 3 comptent, autorisé');

        // 5 transactions dont 1 hors fenêtre : 4 comptent, autorisé
        $result = $this->service->allows_transaction_within_rate_limit(
            accountId: 'ACC-001',
            timestamps: [
                $now - 61,  // expirée
                $now - 45,  // valide
                $now - 30,  // valide
                $now - 20,  // valide
                $now - 10,  // valide
            ],
            now: $now
        );

        $this->assertTrue($result, '1 transaction expirée sur 5 : 4 comptent, autorisé');
    }

    public function test_different_accounts_have_independent_limits(): void
    {
        $now = time();

        $timestamps = [$now - 5, $now - 10, $now - 20, $now - 30, $now - 40];

        // ACC-001 a atteint son seuil
        $resultA = $this->service->allows_transaction_within_rate_limit(
            accountId: 'ACC-001',
            timestamps: $timestamps,
            now: $now
        );

        // ACC-002 n'a aucune transaction
        $resultB = $this->service->allows_transaction_within_rate_limit(
            accountId: 'ACC-002',
            timestamps: [],
            now: $now
        );

        $this->assertFalse($resultA, 'ACC-001 a atteint son seuil : bloqué');
        $this->assertTrue($resultB, 'ACC-002 est indépendant : autorisé');
    }

    public function test_computes_remaining_allowance(): void
    {
        $now = time();

        // 3 transactions valides sur 5 max : reste 2
        $remaining = $this->service->computes_remaining_allowance(
            timestamps: [$now - 10, $now - 20, $now - 30],
            now: $now
        );

        $this->assertSame(2, $remaining, '3 transactions sur 5 : reste 2');

        // 0 transactions : reste 5
        $remaining = $this->service->computes_remaining_allowance(
            timestamps: [],
            now: $now
        );

        $this->assertSame(5, $remaining, 'Aucune transaction : reste 5');

        // 5 transactions valides : reste 0
        $remaining = $this->service->computes_remaining_allowance(
            timestamps: [$now - 5, $now - 15, $now - 25, $now - 35, $now - 45],
            now: $now
        );

        $this->assertSame(0, $remaining, '5 transactions sur 5 : reste 0');

        // 2 transactions dont 1 expirée : 1 valide, reste 4
        $remaining = $this->service->computes_remaining_allowance(
            timestamps: [$now - 90, $now - 20],
            now: $now
        );

        $this->assertSame(4, $remaining, '1 expirée sur 2 : 1 valide, reste 4');
    }
}