<?php

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;
use App\Services\TransactionMonitoringService;

class TransactionMonitoringTest extends TestCase
{
    private TransactionMonitoringService $service;

    protected function setUp(): void
    {
        $this->service = new TransactionMonitoringService();
    }

    public function test_flags_high_value_transaction(): void
    {
        $this->assertFalse(
            $this->service->is_high_value_transaction(9999.99),
            '9 999,99 € est sous le seuil : pas une alerte montant élevé'
        );

        $this->assertTrue(
            $this->service->is_high_value_transaction(10000.00),
            '10 000,00 € atteint le seuil : alerte montant élevé'
        );

        $this->assertTrue(
            $this->service->is_high_value_transaction(25000.00),
            '25 000,00 € dépasse le seuil : alerte montant élevé'
        );
    }

    public function test_detects_velocity_breach(): void
    {
        $now = 1_700_000_000;

        $this->assertFalse(
            $this->service->exceeds_velocity_limit(
                timestamps: [
                    $now - 3600,
                    $now - 7200,
                    $now - 10800,
                    $now - 14400,
                ],
                now: $now,
            ),
            '4 transactions en 24 h : sous le seuil de vélocité'
        );

        $this->assertTrue(
            $this->service->exceeds_velocity_limit(
                timestamps: [
                    $now - 3600,
                    $now - 7200,
                    $now - 10800,
                    $now - 14400,
                    $now - 18000,
                ],
                now: $now,
            ),
            '5 transactions en 24 h : seuil de vélocité atteint'
        );

        $this->assertFalse(
            $this->service->exceeds_velocity_limit(
                timestamps: [
                    $now - 90000,
                    $now - 95000,
                    $now - 3600,
                    $now - 7200,
                    $now - 10800,
                ],
                now: $now,
            ),
            '2 transactions expirées sur 5 : seulement 3 comptent dans la fenêtre'
        );
    }

    public function test_detects_structuring_pattern(): void
    {
        $this->assertFalse(
            $this->service->has_structuring_pattern([9500.00, 9200.00]),
            '2 montants entre 9 000 € et 10 000 € : pas de structuration'
        );

        $this->assertTrue(
            $this->service->has_structuring_pattern([9500.00, 9200.00, 9800.00]),
            '3 montants dans la zone de structuration : alerte'
        );

        $this->assertTrue(
            $this->service->has_structuring_pattern([500.00, 9000.00, 9500.00, 9200.00]),
            'Seuls les montants entre 9 000 € et 10 000 € comptent pour la structuration'
        );

        $this->assertFalse(
            $this->service->has_structuring_pattern([8500.00, 8800.00, 8900.00]),
            'Montants sous 9 000 € : pas de structuration'
        );
    }

    public function test_evaluate_allows_low_risk_transaction(): void
    {
        $now = 1_700_000_000;

        $result = $this->service->evaluate_transaction([
            'counterparty_id'    => 'ENTITY-001',
            'amount'             => 500.00,
            'now'                => $now,
            'recent_timestamps'  => [$now - 3600],
            'recent_amounts'     => [500.00, 200.00],
        ]);

        $this->assertIsArray($result);
        $this->assertSame('ALLOW', $result['decision']);
        $this->assertArrayNotHasKey('alert', $result);
        $this->assertArrayNotHasKey('reason', $result);
    }

    public function test_evaluate_blocks_blacklisted_counterparty(): void
    {
        $now = 1_700_000_000;

        $result = $this->service->evaluate_transaction([
            'counterparty_id'    => 'ENTITY-999',
            'amount'             => 100.00,
            'now'                => $now,
            'recent_timestamps'  => [],
            'recent_amounts'     => [],
        ]);

        $this->assertIsArray($result);
        $this->assertSame('BLOCK', $result['decision']);
        $this->assertSame('BLACKLISTED_COUNTERPARTY', $result['reason']);
    }
}
