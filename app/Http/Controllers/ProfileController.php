<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user()->load('providerPhotos'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        unset($validated['diploma_file']);
        unset($validated['provider_photos']);

        $user = $request->user();
        $user->fill($validated);

        if ($request->hasFile('diploma_file')) {
            if (!empty($user->diploma_file_path)) {
                Storage::disk('public')->delete($user->diploma_file_path);
            }

            $user->diploma_file_path = $request->file('diploma_file')->store('diplomas', 'public');
        }

        if (($validated['role'] ?? $user->role) === 'provider' && $request->hasFile('provider_photos')) {
            foreach ($user->providerPhotos as $photo) {
                Storage::disk('public')->delete($photo->path);
            }
            $user->providerPhotos()->delete();

            foreach (array_slice($request->file('provider_photos'), 0, 4) as $index => $photo) {
                $user->providerPhotos()->create([
                    'path' => $photo->store('provider-products', 'public'),
                    'position' => $index + 1,
                ]);
            }
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
