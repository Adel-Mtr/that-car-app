<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Models\Post;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller
{
    public function store(StorePostRequest $request): RedirectResponse
    {
        if ($request->filled('vehicle_id')) {
            $vehicle = Vehicle::findOrFail($request->integer('vehicle_id'));
            Gate::authorize('update', $vehicle);
        }

        $request->user()->posts()->create([
            ...$request->validated(),
            'published_at' => now(),
        ]);

        return back()->with('success', 'Your update is live.');
    }

    public function destroy(Request $request, Post $post): RedirectResponse
    {
        Gate::authorize('delete', $post);
        $post->delete();

        return back()->with('success', 'Post deleted.');
    }
}
