<?php

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;
use App\Services\KycService;

class KycTest extends TestCase
{
    private KycService $service;

    protected function setUp(): void
    {
        $this->service = new KycService();
    }

    public function test_validate_identity_document(): void
    {
        $this->assertTrue(
            $this->service->validate_identity_document([
                'type'       => 'passport',
                'number'     => 'AB123456',
                'expires_at' => '2030-01-01',
            ]),
            'Un passeport valide non expiré doit retourner true'
        );

        $this->assertFalse(
            $this->service->validate_identity_document([
                'type'       => 'passport',
                'number'     => '',
                'expires_at' => '2030-01-01',
            ]),
            'Un numéro de document vide doit retourner false'
        );

        $this->assertFalse(
            $this->service->validate_identity_document([
                'type'       => 'unknown_type',
                'number'     => 'AB123456',
                'expires_at' => '2030-01-01',
            ]),
            'Un type de document inconnu doit retourner false'
        );
    }

    public function test_rejects_expired_document(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Document expired');

        $this->service->validate_identity_document([
            'type'       => 'passport',
            'number'     => 'AB123456',
            'expires_at' => '2020-01-01',
        ]);
    }

    public function test_compute_kyc_risk_level(): void
    {
        // LOW : pays sûr, compte > 12 mois, montant mensuel < 5000€
        $this->assertSame(
            'LOW',
            $this->service->compute_kyc_risk_level([
                'country'         => 'FR',
                'account_age_months' => 24,
                'monthly_average' => 1200.00,
            ])
        );

        // MEDIUM : pays sûr, compte < 6 mois OU montant entre 5000€ et 20000€
        $this->assertSame(
            'MEDIUM',
            $this->service->compute_kyc_risk_level([
                'country'         => 'FR',
                'account_age_months' => 3,
                'monthly_average' => 1200.00,
            ])
        );

        $this->assertSame(
            'MEDIUM',
            $this->service->compute_kyc_risk_level([
                'country'         => 'FR',
                'account_age_months' => 24,
                'monthly_average' => 8000.00,
            ])
        );

        // HIGH : montant > 20000€ OU pays à risque
        $this->assertSame(
            'HIGH',
            $this->service->compute_kyc_risk_level([
                'country'         => 'FR',
                'account_age_months' => 24,
                'monthly_average' => 25000.00,
            ])
        );

        $this->assertSame(
            'HIGH',
            $this->service->compute_kyc_risk_level([
                'country'         => 'NG',
                'account_age_months' => 24,
                'monthly_average' => 500.00,
            ])
        );
    }

    public function test_approve_kyc_when_all_checks_pass(): void
    {
        $result = $this->service->evaluate_kyc([
            'document' => [
                'type'       => 'passport',
                'number'     => 'AB123456',
                'expires_at' => '2030-01-01',
            ],
            'country'            => 'FR',
            'account_age_months' => 24,
            'monthly_average'    => 1200.00,
        ]);

        $this->assertIsArray($result);
        $this->assertSame('APPROVED', $result['status']);
        $this->assertSame('LOW', $result['risk_level']);
        $this->assertArrayNotHasKey('reason', $result);
    }

    public function test_reject_kyc_on_sanctioned_country(): void
    {
        $result = $this->service->evaluate_kyc([
            'document' => [
                'type'       => 'passport',
                'number'     => 'KP987654',
                'expires_at' => '2030-01-01',
            ],
            'country'            => 'KP',
            'account_age_months' => 24,
            'monthly_average'    => 500.00,
        ]);

        $this->assertIsArray($result);
        $this->assertSame('REJECTED', $result['status']);
        $this->assertSame('SANCTIONED_COUNTRY', $result['reason']);
    }
}