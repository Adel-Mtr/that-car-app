<?php

namespace App\Http\Controllers;

use App\Actions\Vehicles\CreateVehicle;
use App\Actions\Vehicles\RecalculateVehicleHealth;
use App\Exceptions\VehicleLookupException;
use App\Http\Requests\StoreVehicleRequest;
use App\Http\Requests\UpdateVehicleRequest;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class VehicleController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $vehicles = $user->accessibleVehiclesQuery()
            ->withCount(['maintenanceRecords as open_items_count' => fn ($query) => $query->where('status', 'planned')])
            ->orderBy('make')
            ->orderBy('model')
            ->get();

        return view('vehicles.index', compact('vehicles'));
    }

    public function create(): View
    {
        Gate::authorize('create', Vehicle::class);

        return view('vehicles.create');
    }

    public function store(StoreVehicleRequest $request, CreateVehicle $createVehicle): RedirectResponse
    {
        try {
            $vehicle = $createVehicle->handle($request->user(), $request->validated());
        } catch (VehicleLookupException $exception) {
            return back()
                ->withInput()
                ->withErrors(['registration' => $exception->getMessage()]);
        }

        return redirect()
            ->route('vehicles.show', $vehicle)
            ->with('success', $vehicle->displayName().' has been added to your garage.');
    }

    public function show(Vehicle $vehicle, RecalculateVehicleHealth $recalculateVehicleHealth): View
    {
        Gate::authorize('view', $vehicle);

        $vehicle->load([
            'maintenanceRecords' => fn ($query) => $query->latest('completed_at')->orderBy('due_at'),
            'reminders' => fn ($query) => $query->whereNull('completed_at')->orderBy('due_at'),
            'documents' => fn ($query) => $query->latest('document_date'),
            'motTests' => fn ($query) => $query->with('defects')->latest('completed_at'),
            'members:id,name,username,avatar_url',
        ]);
        $health = $recalculateVehicleHealth->handle($vehicle);

        return view('vehicles.show', compact('vehicle', 'health'));
    }

    public function update(UpdateVehicleRequest $request, Vehicle $vehicle, RecalculateVehicleHealth $recalculateVehicleHealth): RedirectResponse
    {
        $vehicle->update($request->validated());
        $recalculateVehicleHealth->handle($vehicle->fresh());

        return back()->with('success', 'Vehicle details updated.');
    }

    public function destroy(Vehicle $vehicle): RedirectResponse
    {
        Gate::authorize('delete', $vehicle);
        $vehicle->delete();

        return redirect()->route('vehicles.index')->with('success', 'Vehicle removed from your garage.');
    }
}
