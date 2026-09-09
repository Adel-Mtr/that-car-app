<?php

namespace App\Http\Controllers;

use App\Models\Specialist;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SpecialistController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'category' => ['nullable', 'string', 'in:servicing,tyres,bodywork,detailing,performance,electric,classic'],
            'city' => ['nullable', 'string', 'max:80'],
        ]);

        $specialists = Specialist::query()
            ->when($filters['category'] ?? null, fn ($query, $category) => $query->whereJsonContains('categories', $category))
            ->when($filters['city'] ?? null, fn ($query, $city) => $query->where('city', 'like', '%'.$city.'%'))
            ->withCount('bookings')
            ->orderByDesc('is_featured')
            ->orderByDesc('rating')
            ->paginate(9)
            ->withQueryString();

        return view('specialists.index', compact('specialists', 'filters'));
    }

    public function show(Request $request, Specialist $specialist): View
    {
        $vehicles = $request->user()->manageableVehiclesQuery()
            ->select(['id', 'make', 'model', 'registration'])
            ->orderBy('make')
            ->get();

        return view('specialists.show', compact('specialist', 'vehicles'));
    }
}
