<?php

namespace App\Actions\Vehicles;

use App\Models\Vehicle;
use App\Services\VehicleHealthService;

class RecalculateVehicleHealth
{
    public function __construct(private VehicleHealthService $healthService) {}

    /** @return array{score: int, status: string, label: string, reasons: array<int, array{severity: string, title: string, detail: string}>} */
    public function handle(Vehicle $vehicle): array
    {
        $assessment = $this->healthService->assess($vehicle);

        if ($vehicle->health_score !== $assessment['score']) {
            $vehicle->update(['health_score' => $assessment['score']]);
        }

        return $assessment;
    }
}
