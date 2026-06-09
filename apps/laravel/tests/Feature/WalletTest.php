<?php

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;
use App\Services\WalletService;

class WalletTest extends TestCase
{
    private WalletService $service;

    protected function setUp(): void
    {
        $this->service = new WalletService();
    }

    public function test_get_available_balance(): void
    {
        // Solde total 1000€, deux transactions en attente de 150€ et 200€
        $balance = $this->service->get_available_balance(
            total: 1000.00,
            pending: [150.00, 200.00]
        );

        $this->assertSame(650.00, $balance);

        // Aucune transaction en attente
        $balance = $this->service->get_available_balance(
            total: 500.00,
            pending: []
        );

        $this->assertSame(500.00, $balance);

        // Transactions en attente égales au solde total
        $balance = $this->service->get_available_balance(
            total: 300.00,
            pending: [200.00, 100.00]
        );

        $this->assertSame(0.00, $balance);
    }

    public function test_debit(): void
    {
        $result = $this->service->debit(
            balance: 800.00,
            amount: 250.00,
            label: 'Paiement facture F-2024-089'
        );

        $this->assertIsArray($result);
        $this->assertSame(550.00, $result['balance']);
        $this->assertSame(250.00, $result['amount']);
        $this->assertSame('debit', $result['type']);
        $this->assertSame('Paiement facture F-2024-089', $result['label']);
        $this->assertArrayHasKey('reference', $result);
    }

    public function test_debit_throws_on_insufficient_funds(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Insufficient funds');

        $this->service->debit(
            balance: 100.00,
            amount: 250.00,
            label: 'Tentative dépassement'
        );
    }

    public function test_credit(): void
    {
        $result = $this->service->credit(
            balance: 400.00,
            amount: 600.00,
            label: 'Virement entrant client'
        );

        $this->assertIsArray($result);
        $this->assertSame(1000.00, $result['balance']);
        $this->assertSame(600.00, $result['amount']);
        $this->assertSame('credit', $result['type']);
        $this->assertSame('Virement entrant client', $result['label']);
        $this->assertArrayHasKey('reference', $result);
    }

    public function test_daily_debit_limit(): void
    {
        // Cumul 3000€ déjà débités, nouveau débit de 1999€ : autorisé (cumul = 4999€ < 5000€)
        $this->assertTrue(
            $this->service->daily_debit_limit(
                amount: 1999.00,
                debited_today: [1000.00, 2000.00]
            )
        );

        // Cumul 3000€, nouveau débit de 2000€ : refusé (cumul = 5000€ = plafond)
        $this->assertFalse(
            $this->service->daily_debit_limit(
                amount: 2000.00,
                debited_today: [1000.00, 2000.00]
            )
        );

        // Aucun débit du jour, débit de 5000€ exactement : refusé
        $this->assertFalse(
            $this->service->daily_debit_limit(
                amount: 5000.00,
                debited_today: []
            )
        );

        // Aucun débit du jour, débit de 4999.99€ : autorisé
        $this->assertTrue(
            $this->service->daily_debit_limit(
                amount: 4999.99,
                debited_today: []
            )
        );
    }
}