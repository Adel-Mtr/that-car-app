<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVehicleDocumentRequest;
use App\Models\Vehicle;
use App\Models\VehicleDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VehicleDocumentController extends Controller
{
    public function store(StoreVehicleDocumentRequest $request, Vehicle $vehicle): RedirectResponse
    {
        $file = $request->file('document');
        $path = $file->store('vehicle-documents/'.$vehicle->public_id);

        $vehicle->documents()->create([
            ...$request->safe()->only(['title', 'type', 'document_date']),
            'uploaded_by' => $request->user()->id,
            'disk' => config('filesystems.default'),
            'path' => $path,
            'original_filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType() ?? 'application/octet-stream',
            'size_bytes' => $file->getSize(),
        ]);

        return back()->with('success', 'Document added securely.');
    }

    public function show(Vehicle $vehicle, VehicleDocument $vehicleDocument): StreamedResponse
    {
        abort_unless($vehicleDocument->vehicle_id === $vehicle->id, 404);
        Gate::authorize('view', $vehicleDocument);

        return Storage::disk($vehicleDocument->disk)->download(
            $vehicleDocument->path,
            $vehicleDocument->original_filename,
        );
    }

    public function destroy(Vehicle $vehicle, VehicleDocument $vehicleDocument): RedirectResponse
    {
        abort_unless($vehicleDocument->vehicle_id === $vehicle->id, 404);
        Gate::authorize('delete', $vehicleDocument);

        Storage::disk($vehicleDocument->disk)->delete($vehicleDocument->path);
        $vehicleDocument->delete();

        return back()->with('success', 'Document deleted.');
    }
}
