<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['nullable', 'in:user,technician,provider'],
            'specialty' => ['nullable', 'string', 'max:255', 'required_if:role,technician'],
            'spare_parts_type' => ['nullable', 'in:washing,refrigerators,refrigeration_systems,mixed', 'required_if:role,provider'],
            'country' => ['nullable', 'string', 'max:120', 'required_if:role,technician,provider'],
            'state' => ['nullable', 'string', 'max:120', 'required_if:role,technician,provider'],
            'years_experience' => ['nullable', 'integer', 'min:0', 'max:70'],
            'phone' => ['nullable', 'string', 'max:30', 'required_if:role,technician,provider'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'studies' => ['nullable', 'string', 'max:500'],
            'diplomas' => ['nullable', 'string', 'max:500'],
            'diploma_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:4096'],
            'provider_photos' => ['nullable', 'array', 'max:4'],
            'provider_photos.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'address' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        $diplomaFilePath = null;
        if ($request->hasFile('diploma_file')) {
            $diplomaFilePath = $request->file('diploma_file')->store('diplomas', 'public');
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'] ?? 'user',
            'specialty' => $validated['specialty'] ?? null,
            'spare_parts_type' => $validated['spare_parts_type'] ?? null,
            'country' => $validated['country'] ?? null,
            'state' => $validated['state'] ?? null,
            'years_experience' => $validated['years_experience'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'whatsapp' => $validated['whatsapp'] ?? null,
            'studies' => $validated['studies'] ?? null,
            'diplomas' => $validated['diplomas'] ?? null,
            'diploma_file_path' => $diplomaFilePath,
            'address' => $validated['address'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
        ]);

        if (($validated['role'] ?? 'user') === 'provider' && $request->hasFile('provider_photos')) {
            foreach (array_slice($request->file('provider_photos'), 0, 4) as $index => $photo) {
                $user->providerPhotos()->create([
                    'path' => $photo->store('provider-products', 'public'),
                    'position' => $index + 1,
                ]);
            }
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
