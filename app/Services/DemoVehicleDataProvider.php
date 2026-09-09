<?php

namespace App\Services;

use App\Contracts\VehicleDataProvider;
use Illuminate\Support\Str;

class DemoVehicleDataProvider implements VehicleDataProvider
{
    /** @var array<int, array{make: string, model: string, variant: string, fuel: string, transmission: string, engine: int}> */
    private const VEHICLES = [
        ['make' => 'Volkswagen', 'model' => 'Golf', 'variant' => 'GTI', 'fuel' => 'Petrol', 'transmission' => 'Automatic', 'engine' => 1984],
        ['make' => 'BMW', 'model' => '3 Series', 'variant' => '320d M Sport', 'fuel' => 'Diesel', 'transmission' => 'Automatic', 'engine' => 1995],
        ['make' => 'Ford', 'model' => 'Focus', 'variant' => 'ST-Line', 'fuel' => 'Petrol', 'transmission' => 'Manual', 'engine' => 1498],
        ['make' => 'Tesla', 'model' => 'Model 3', 'variant' => 'Long Range', 'fuel' => 'Electric', 'transmission' => 'Automatic', 'engine' => 0],
        ['make' => 'Toyota', 'model' => 'Corolla', 'variant' => 'Design Hybrid', 'fuel' => 'Hybrid', 'transmission' => 'Automatic', 'engine' => 1798],
    ];

    /**
     * @return array<string, mixed>
     */
    public function lookup(string $registration): array
    {
        $registration = Str::upper((string) preg_replace('/[^A-Z0-9]/i', '', $registration));
        $seed = abs(crc32($registration));
        $vehicle = self::VEHICLES[$seed % count(self::VEHICLES)];
        $colours = ['Deep blue', 'Graphite grey', 'Pearl white', 'Midnight black', 'Racing red'];
        $motDueAt = today()->addDays(42 + ($seed % 150));
        $taxDueAt = today()->addDays(28 + ($seed % 210));
        $lastTestAt = $motDueAt->copy()->subYear();
        $previousTestAt = $lastTestAt->copy()->subYear();
        $baseMileage = 28000 + ($seed % 46000);

        return [
            'registration' => $registration,
            'make' => $vehicle['make'],
            'model' => $vehicle['model'],
            'variant' => $vehicle['variant'],
            'year' => 2017 + ($seed % 8),
            'colour' => $colours[$seed % count($colours)],
            'fuel_type' => $vehicle['fuel'],
            'transmission' => $vehicle['transmission'],
            'engine_size_cc' => $vehicle['engine'] ?: null,
            'mot_status' => 'valid',
            'mot_due_at' => $motDueAt->toDateString(),
            'tax_status' => 'taxed',
            'tax_due_at' => $taxDueAt->toDateString(),
            'valuation_pence' => 850000 + (($seed % 320) * 5000),
            'mot_tests' => [
                [
                    'test_number' => 'D'.str_pad((string) ($seed % 100000000), 8, '0', STR_PAD_LEFT),
                    'completed_at' => $lastTestAt->setTime(10, 30)->toDateTimeString(),
                    'expiry_at' => $motDueAt->toDateString(),
                    'result' => 'passed',
                    'odometer_value' => $baseMileage,
                    'odometer_unit' => 'mi',
                    'defects' => [
                        [
                            'text' => 'Nearside front tyre worn close to the legal limit',
                            'type' => 'advisory',
                            'dangerous' => false,
                        ],
                    ],
                ],
                [
                    'test_number' => 'D'.str_pad((string) (($seed + 773) % 100000000), 8, '0', STR_PAD_LEFT),
                    'completed_at' => $previousTestAt->setTime(9, 45)->toDateTimeString(),
                    'expiry_at' => $lastTestAt->copy()->addDays(10)->toDateString(),
                    'result' => 'passed',
                    'odometer_value' => max(1000, $baseMileage - 7800),
                    'odometer_unit' => 'mi',
                    'defects' => [],
                ],
            ],
            'provider' => 'demo',
        ];
    }
}
