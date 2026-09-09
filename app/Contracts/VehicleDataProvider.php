<?php

namespace App\Contracts;

interface VehicleDataProvider
{
    /**
     * @return array{
     *     registration: string,
     *     make: string,
     *     model: string,
     *     variant: string|null,
     *     year: int,
     *     colour: string,
     *     fuel_type: string,
     *     transmission: string,
     *     engine_size_cc: int|null,
     *     mot_status: string,
     *     mot_due_at: string|null,
     *     tax_status: string,
     *     tax_due_at: string|null,
     *     valuation_pence: int|null,
     *     mot_tests: array<int, array<string, mixed>>,
     *     provider: string
     * }
     */
    public function lookup(string $registration): array;
}
