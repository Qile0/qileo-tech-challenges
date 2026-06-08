<?php

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;
use App\Services\FraudDetectionService;

class FraudDetectionTest extends TestCase
{
    private FraudDetectionService $service;

    protected function setUp(): void
    {
        $this->service = new FraudDetectionService();
    }

    public function test_compute_risk_score(): void
    {
        // Score de base : montant élevé +30, pays à risque +25, heure nocturne +15, nouveau bénéficiaire +10
        $this->assertSame(
            30,
            $this->service->compute_risk_score([
                'amount'        => 8500.00,
                'country'       => 'FR',
                'hour'          => 14,
                'new_recipient' => false,
            ]),
            'Montant > 5000€ seul : score = 30'
        );

        $this->assertSame(
            80,
            $this->service->compute_risk_score([
                'amount'        => 8500.00,
                'country'       => 'NG',
                'hour'          => 3,
                'new_recipient' => true,
            ]),
            'Montant > 5000€ + pays risque + heure nocturne + nouveau bénéficiaire : score = 80'
        );

        $this->assertSame(
            0,
            $this->service->compute_risk_score([
                'amount'        => 100.00,
                'country'       => 'FR',
                'hour'          => 10,
                'new_recipient' => false,
            ]),
            'Transaction normale : score = 0'
        );

        $this->assertSame(
            40,
            $this->service->compute_risk_score([
                'amount'        => 100.00,
                'country'       => 'NG',
                'hour'          => 2,
                'new_recipient' => false,
            ]),
            'Pays risque + heure nocturne : score = 40'
        );
    }

    public function test_is_velocity_breach(): void
    {
        // Vélocité : max 3 transactions en 1 heure OU max 10 000€ cumulés en 24h
        $this->assertFalse(
            $this->service->is_velocity_breach([
                'tx_last_hour'    => 2,
                'amount_last_24h' => 4000.00,
                'amount'          => 500.00,
            ]),
            '2 tx/h et 4500€/24h : pas de breach'
        );

        $this->assertTrue(
            $this->service->is_velocity_breach([
                'tx_last_hour'    => 3,
                'amount_last_24h' => 2000.00,
                'amount'          => 100.00,
            ]),
            '3 tx/h déjà atteint : breach'
        );

        $this->assertTrue(
            $this->service->is_velocity_breach([
                'tx_last_hour'    => 1,
                'amount_last_24h' => 9800.00,
                'amount'          => 300.00,
            ]),
            'Cumul 24h dépasserait 10000€ : breach'
        );

        $this->assertFalse(
            $this->service->is_velocity_breach([
                'tx_last_hour'    => 1,
                'amount_last_24h' => 9700.00,
                'amount'          => 300.00,
            ]),
            'Cumul exactement 10000€ : pas de breach'
        );
    }

    public function test_flag_suspicious_country(): void
    {
        // Pays à haut risque : NG, IR, KP, SY, MM, BY
        $this->assertTrue(
            $this->service->flag_suspicious_country('NG'),
            'Nigeria : pays à risque'
        );

        $this->assertTrue(
            $this->service->flag_suspicious_country('KP'),
            'Corée du Nord : pays à risque'
        );

        $this->assertFalse(
            $this->service->flag_suspicious_country('FR'),
            'France : pays sûr'
        );

        $this->assertFalse(
            $this->service->flag_suspicious_country('DE'),
            'Allemagne : pays sûr'
        );

        $this->assertTrue(
            $this->service->flag_suspicious_country('ng'),
            'Code pays en minuscule : doit fonctionner'
        );
    }

    public function test_evaluate_transaction(): void
    {
        // APPROVED : score < 50 et pas de velocity breach
        $result = $this->service->evaluate_transaction([
            'amount'          => 200.00,
            'country'         => 'FR',
            'hour'            => 10,
            'new_recipient'   => false,
            'tx_last_hour'    => 1,
            'amount_last_24h' => 500.00,
        ]);
        $this->assertSame('APPROVED', $result['decision']);
        $this->assertSame(0, $result['risk_score']);
        $this->assertFalse($result['velocity_breach']);

        // REVIEW : score >= 50 mais pas de velocity breach
        $result = $this->service->evaluate_transaction([
            'amount'          => 8500.00,
            'country'         => 'NG',
            'hour'            => 3,
            'new_recipient'   => false,
            'tx_last_hour'    => 1,
            'amount_last_24h' => 500.00,
        ]);
        $this->assertSame('REVIEW', $result['decision']);
        $this->assertSame(55, $result['risk_score']);

        // BLOCKED : velocity breach (peu importe le score)
        $result = $this->service->evaluate_transaction([
            'amount'          => 100.00,
            'country'         => 'FR',
            'hour'            => 10,
            'new_recipient'   => false,
            'tx_last_hour'    => 3,
            'amount_last_24h' => 500.00,
        ]);
        $this->assertSame('BLOCKED', $result['decision']);
        $this->assertTrue($result['velocity_breach']);
    }

    public function test_reconcile_alerts(): void
    {
        $batch = [
            ['id' => 'TX001', 'decision' => 'APPROVED', 'risk_score' => 0],
            ['id' => 'TX002', 'decision' => 'REVIEW',   'risk_score' => 55],
            ['id' => 'TX003', 'decision' => 'BLOCKED',  'risk_score' => 80],
            ['id' => 'TX004', 'decision' => 'REVIEW',   'risk_score' => 60],
            ['id' => 'TX005', 'decision' => 'APPROVED', 'risk_score' => 10],
        ];

        $result = $this->service->reconcile_alerts($batch);

        $this->assertSame(2, $result['approved_count']);
        $this->assertSame(2, $result['review_count']);
        $this->assertSame(1, $result['blocked_count']);
        $this->assertSame(57.5, $result['average_risk_score']);
        $this->assertSame('TX003', $result['highest_risk_id']);
    }
}