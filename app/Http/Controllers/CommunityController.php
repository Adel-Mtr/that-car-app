<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CommunityController extends Controller
{
    public function __invoke(Request $request): View
    {
        $posts = Post::query()
            ->where('visibility', 'public')
            ->whereNotNull('published_at')
            ->with([
                'user:id,name,username,avatar_url',
                'vehicle:id,make,model,year,public_id,visibility',
            ])
            ->latest('published_at')
            ->latest('id')
            ->paginate(12);

        $vehicles = $request->user()->manageableVehiclesQuery()
            ->select(['id', 'make', 'model', 'registration'])
            ->orderBy('make')
            ->get();

        return view('community.index', compact('posts', 'vehicles'));
    }
}
