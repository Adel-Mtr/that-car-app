<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', ['user' => $request->user()]);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $attributes = $request->safe()->only(['name', 'username', 'bio', 'postcode']);
        $attributes['preferences'] = [
            ...($request->user()->preferences ?? []),
            'primary_goal' => $request->string('primary_goal')->toString(),
            'email_reminders' => $request->boolean('email_reminders'),
        ];
        $attributes['onboarding_completed'] = true;

        $request->user()->update($attributes);

        return back()->with('success', 'Profile preferences saved.');
    }
}
