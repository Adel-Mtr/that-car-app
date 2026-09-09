<?php

namespace App\Actions\Vehicles;

use App\Contracts\VehicleDataProvider;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateVehicle
{
    public function __construct(
        private VehicleDataProvider $vehicleDataProvider,
        private RecalculateVehicleHealth $recalculateVehicleHealth,
    ) {}

    /**
     * @param  array{registration: string, current_mileage?: int|null, insurance_due_at?: string|null, visibility?: string|null}  $attributes
     */
    public function handle(User $owner, array $attributes): Vehicle
    {
        $lookup = $this->vehicleDataProvider->lookup($attributes['registration']);

        return DB::transaction(function () use ($owner, $attributes, $lookup): Vehicle {
            $vehicle = Vehicle::create([
                ...Arr::only($lookup, [
                    'registration',
                    'make',
                    'model',
                    'variant',
                    'year',
                    'colour',
                    'fuel_type',
                    'transmission',
                    'engine_size_cc',
                    'mot_status',
                    'mot_due_at',
                    'tax_status',
                    'tax_due_at',
                    'valuation_pence',
                ]),
                'owner_id' => $owner->id,
                'public_id' => (string) Str::uuid(),
                'current_mileage' => $attributes['current_mileage'] ?? 0,
                'insurance_due_at' => $attributes['insurance_due_at'] ?? null,
                'visibility' => $attributes['visibility'] ?? 'private',
                'metadata' => ['provider' => $lookup['provider']],
                'last_synced_at' => now(),
            ]);

            foreach ($lookup['mot_tests'] as $testData) {
                $defects = $testData['defects'] ?? [];
                unset($testData['defects']);

                $motTest = $vehicle->motTests()->create($testData);

                foreach ($defects as $defectData) {
                    $maintenanceRecord = $vehicle->maintenanceRecords()->create([
                        'created_by' => $owner->id,
                        'type' => 'mot_advisory',
                        'status' => 'planned',
                        'urgency' => $defectData['dangerous'] ? 'critical' : 'attention',
                        'title' => Str::headline(Str::limit($defectData['text'], 70, '')),
                        'description' => $defectData['text'],
                        'source' => 'mot',
                        'due_at' => today()->addMonths(3),
                        'verification_status' => 'government_data',
                    ]);

                    $motTest->defects()->create([
                        ...$defectData,
                        'maintenance_record_id' => $maintenanceRecord->id,
                    ]);
                }
            }

            $this->createLegalReminder($vehicle, $owner, 'mot', 'MOT renewal', $vehicle->mot_due_at);
            $this->createLegalReminder($vehicle, $owner, 'tax', 'Vehicle tax renewal', $vehicle->tax_due_at);
            $this->createLegalReminder($vehicle, $owner, 'insurance', 'Insurance renewal', $vehicle->insurance_due_at);

            $vehicle->load(['maintenanceRecords', 'motTests.defects']);
            $this->recalculateVehicleHealth->handle($vehicle);

            return $vehicle;
        });
    }

    private function createLegalReminder(Vehicle $vehicle, User $owner, string $category, string $title, mixed $dueAt): void
    {
        if ($dueAt === null) {
            return;
        }

        $vehicle->reminders()->create([
            'user_id' => $owner->id,
            'category' => $category,
            'title' => $title,
            'due_at' => $dueAt,
            'lead_days' => 30,
        ]);
    }
}
