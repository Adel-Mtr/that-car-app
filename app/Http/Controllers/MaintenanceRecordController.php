<?php

namespace App\Http\Controllers;

use App\Actions\Vehicles\RecalculateVehicleHealth;
use App\Http\Requests\StoreMaintenanceRecordRequest;
use App\Models\MaintenanceRecord;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class MaintenanceRecordController extends Controller
{
    public function store(StoreMaintenanceRecordRequest $request, Vehicle $vehicle, RecalculateVehicleHealth $recalculateVehicleHealth): RedirectResponse
    {
        $attributes = $request->safe()->except('cost');
        $attributes['created_by'] = $request->user()->id;
        $attributes['source'] = 'owner';
        $attributes['verification_status'] = 'owner_entered';
        $attributes['cost_pence'] = $request->filled('cost')
            ? (int) round($request->float('cost') * 100)
            : null;

        if ($attributes['status'] === 'completed' && empty($attributes['completed_at'])) {
            $attributes['completed_at'] = today();
        }

        $vehicle->maintenanceRecords()->create($attributes);
        $recalculateVehicleHealth->handle($vehicle->fresh());

        return back()->with('success', 'Maintenance item added to the vehicle timeline.');
    }

    public function destroy(Vehicle $vehicle, MaintenanceRecord $maintenanceRecord, RecalculateVehicleHealth $recalculateVehicleHealth): RedirectResponse
    {
        abort_unless($maintenanceRecord->vehicle_id === $vehicle->id, 404);
        Gate::authorize('delete', $maintenanceRecord);

        $maintenanceRecord->delete();
        $recalculateVehicleHealth->handle($vehicle->fresh());

        return back()->with('success', 'Maintenance item removed.');
    }
}
