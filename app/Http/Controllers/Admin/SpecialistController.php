<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Specialist;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SpecialistController extends Controller
{
    public function index(): View
    {
        $specialists = Specialist::query()
            ->withCount('bookings')
            ->orderByDesc('is_featured')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.specialists.index', compact('specialists'));
    }

    public function create(): View
    {
        return view('admin.specialists.create', ['specialist' => new Specialist]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($data['name']);
        Specialist::create($data);

        return redirect()->route('admin.specialists.index')->with('success', 'Specialist created.');
    }

    public function edit(Specialist $specialist): View
    {
        return view('admin.specialists.edit', compact('specialist'));
    }

    public function update(Request $request, Specialist $specialist): RedirectResponse
    {
        $data = $this->validated($request);
        if ($specialist->name !== $data['name']) {
            $data['slug'] = $this->uniqueSlug($data['name'], $specialist);
        }
        $specialist->update($data);

        return redirect()->route('admin.specialists.index')->with('success', 'Specialist updated.');
    }

    public function destroy(Specialist $specialist): RedirectResponse
    {
        if ($specialist->bookings()->exists()) {
            return back()->withErrors(['specialist' => 'Specialists with booking history cannot be deleted. Unfeature or unverify them instead.']);
        }

        $specialist->delete();

        return redirect()->route('admin.specialists.index')->with('success', 'Specialist deleted.');
    }

    /** @return array<string, mixed> */
    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'tagline' => ['required', 'string', 'max:180'],
            'description' => ['required', 'string', 'max:10000'],
            'categories' => ['required', 'string', 'max:1000'],
            'brands' => ['nullable', 'string', 'max:1000'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:120'],
            'postcode' => ['required', 'string', 'max:16'],
            'price_level' => ['required', Rule::in(['£', '££', '£££'])],
            'phone' => ['nullable', 'string', 'max:32'],
            'email' => ['nullable', 'email', 'max:255'],
            'website_url' => ['nullable', 'url', 'max:2048'],
            'image_url' => ['nullable', 'url', 'max:2048'],
            'is_verified' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
        ]);

        $data['categories'] = $this->commaSeparated($data['categories']) ?? [];
        $data['brands'] = $this->commaSeparated($data['brands'] ?? null);
        $data['is_verified'] = $request->boolean('is_verified');
        $data['is_featured'] = $request->boolean('is_featured');

        return $data;
    }

    private function uniqueSlug(string $name, ?Specialist $ignore = null): string
    {
        $base = Str::slug($name) ?: 'specialist';
        $slug = $base;
        $suffix = 2;

        while (Specialist::query()->where('slug', $slug)->when($ignore, fn ($query) => $query->whereKeyNot($ignore->getKey()))->exists()) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }

    /** @return list<string>|null */
    private function commaSeparated(?string $value): ?array
    {
        $items = collect(explode(',', (string) $value))
            ->map(fn (string $item): string => trim($item))
            ->filter()
            ->unique()
            ->values()
            ->all();

        return $items === [] ? null : $items;
    }
}
