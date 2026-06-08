<?php

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;
use App\Services\SepaTransferService;

class SepaTransferTest extends TestCase
{
    private SepaTransferService $service;

    protected function setUp(): void
    {
        $this->service = new SepaTransferService();
    }

    public function test_validate_iban(): void
    {
        $this->assertTrue(
            $this->service->validate_iban('FR7630006000011234567890189'),
            'Un IBAN français valide doit retourner true'
        );

        $this->assertFalse(
            $this->service->validate_iban('FR00INVALID123'),
            'Un IBAN malformé doit retourner false'
        );

        $this->assertFalse(
            $this->service->validate_iban(''),
            'Un IBAN vide doit retourner false'
        );
    }

    public function test_calculate_fee(): void
    {
        $this->assertSame(
            0.00,
            $this->service->calculate_fee(500.00),
            'Virement < 1000€ : frais = 0.00€'
        );

        $this->assertSame(
            0.50,
            $this->service->calculate_fee(1000.00),
            'Virement = 1000€ : frais = 0.50€'
        );

        $this->assertSame(
            0.50,
            $this->service->calculate_fee(5000.00),
            'Virement entre 1000€ et 10000€ : frais = 0.50€'
        );

        $this->assertSame(
            2.00,
            $this->service->calculate_fee(10001.00),
            'Virement > 10000€ : frais = 2.00€'
        );
    }

    public function test_check_daily_limit(): void
    {
        $this->assertTrue(
            $this->service->check_daily_limit(4000.00, [10000.00, 20000.00]),
            'Cumul 34000€ < 50000€ : autorisé'
        );

        $this->assertFalse(
            $this->service->check_daily_limit(10000.00, [25000.00, 20000.00]),
            'Cumul 55000€ > 50000€ : refusé'
        );

        $this->assertTrue(
            $this->service->check_daily_limit(50000.00, []),
            'Premier virement exactement au plafond : autorisé'
        );

        $this->assertFalse(
            $this->service->check_daily_limit(0.01, [50000.00]),
            'Plafond déjà atteint : refusé'
        );
    }

    public function test_create_transfer(): void
    {
        $transfer = $this->service->create_transfer(
            'FR7630006000011234567890189',
            250.00,
            'Remboursement facture 2024-042'
        );

        $this->assertIsArray($transfer);
        $this->assertSame('pending', $transfer['status']);
        $this->assertSame(250.00, $transfer['amount']);
        $this->assertSame('FR7630006000011234567890189', $transfer['iban']);
        $this->assertArrayHasKey('reference', $transfer);
        $this->assertArrayHasKey('fee', $transfer);

        $invalid = $this->service->create_transfer(
            'FR7630006000011234567890189',
            -50.00,
            'Test montant invalide'
        );

        $this->assertSame('INVALID_AMOUNT', $invalid['error']);
    }

    public function test_reconcile_batch(): void
    {
        $batch = [
            'batch_id' => 'BATCH-2024-001',
            'committed_amount' => 5000.00,
            'entries' => [
                ['id' => 'VIR001', 'amount' => 2000.00, 'status' => 'ACCP'],
                ['id' => 'VIR002', 'amount' => 1500.00, 'status' => 'RJCT'],
                ['id' => 'VIR003', 'amount' => 1500.00, 'status' => 'ACCP'],
            ],
        ];

        $result = $this->service->reconcile_batch($batch);

        $this->assertSame(3500.00, $result['accepted_total']);
        $this->assertSame(1500.00, $result['rejected_total']);
        $this->assertFalse($result['anomaly']);

        $batchMismatch = [
            'batch_id' => 'BATCH-2024-002',
            'committed_amount' => 5000.00,
            'entries' => [
                ['id' => 'VIR004', 'amount' => 3000.00, 'status' => 'ACCP'],
            ],
        ];

        $result2 = $this->service->reconcile_batch($batchMismatch);

        $this->assertTrue($result2['anomaly']);
        $this->assertSame('AMOUNT_MISMATCH', $result2['anomaly_detail']);
    }
}