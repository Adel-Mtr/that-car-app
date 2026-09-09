<?php

namespace App\Http\Controllers;

use App\Actions\Vehicles\RecalculateVehicleHealth;
use App\Models\MaintenanceRecord;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class CompletedMaintenanceRecordController extends Controller
{
    public function store(Vehicle $vehicle, MaintenanceRecord $maintenanceRecord, RecalculateVehicleHealth $recalculateVehicleHealth): RedirectResponse
    {
        abort_unless($maintenanceRecord->vehicle_id === $vehicle->id, 404);
        Gate::authorize('update', $maintenanceRecord);

        $maintenanceRecord->update([
            'status' => 'completed',
            'completed_at' => today(),
        ]);
        $maintenanceRecord->motDefects()->update(['resolved_at' => now()]);

        if ($maintenanceRecord->mileage !== null && $maintenanceRecord->mileage > $vehicle->current_mileage) {
            $vehicle->update(['current_mileage' => $maintenanceRecord->mileage]);
        }

        $recalculateVehicleHealth->handle($vehicle->fresh());

        return back()->with('success', 'Work marked as complete. Your health score has been refreshed.');
    }
}
