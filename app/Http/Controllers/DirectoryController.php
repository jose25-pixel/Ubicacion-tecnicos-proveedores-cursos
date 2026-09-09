<?php

namespace App\Http\Controllers;

use App\Models\ProfileView;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DirectoryController extends Controller
{
    public function index(Request $request): View
    {
        $role = $request->string('role')->toString();
        $q = trim((string) $request->string('q'));
        $verifiedOnly = (bool) $request->boolean('verified_only');
        $lat = $request->filled('lat') ? (float) $request->input('lat') : null;
        $lng = $request->filled('lng') ? (float) $request->input('lng') : null;

        if ($lat === null || $lng === null) {
            $authUser = $request->user();
            if ($authUser !== null && $authUser->latitude !== null && $authUser->longitude !== null) {
                $lat = (float) $authUser->latitude;
                $lng = (float) $authUser->longitude;
            }
        }
        $radius = (float) $request->input('radius', 50);

        $query = User::query()
            ->with('providerPhotos')
            ->whereIn('role', ['technician', 'provider'])
            ->when(in_array($role, ['technician', 'provider'], true), fn (Builder $builder) => $builder->where('role', $role))
            ->when($verifiedOnly, function (Builder $builder) {
                $builder->where(function (Builder $inner) {
                    $inner->where('role', 'provider')
                        ->orWhere(function (Builder $tech) {
                            $tech->where('role', 'technician')->whereNotNull('technician_verified_at');
                        });
                });
            })
            ->when($q !== '', function (Builder $builder) use ($q) {
                $builder->where(function (Builder $inner) use ($q) {
                    $inner->where('name', 'like', "%{$q}%")
                        ->orWhere('specialty', 'like', "%{$q}%")
                        ->orWhere('spare_parts_type', 'like', "%{$q}%")
                        ->orWhere('country', 'like', "%{$q}%")
                        ->orWhere('state', 'like', "%{$q}%");
                });
            });

        if ($lat !== null && $lng !== null) {
            $distanceSql = '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude))))';

            $query
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->select('users.*')
                ->selectRaw("{$distanceSql} as distance_km", [$lat, $lng, $lat])
                ->having('distance_km', '<=', $radius)
                ->orderBy('distance_km');
        } else {
            $query->latest('updated_at');
        }

        $members = $query->paginate(12)->withQueryString();

        return view('directory.index', [
            'members' => $members,
            'filters' => [
                'role' => $role,
                'q' => $q,
                'verified_only' => $verifiedOnly,
                'lat' => $lat,
                'lng' => $lng,
                'radius' => $radius,
            ],
        ]);
    }

    public function show(User $user): View|RedirectResponse
    {
        abort_unless(in_array($user->role, ['technician', 'provider'], true), 404);

        if (! auth()->check()) {
            return redirect()->route('register');
        }

        ProfileView::create([
            'viewed_user_id' => $user->id,
            'viewer_user_id' => auth()->id(),
            'viewer_ip' => request()->ip(),
        ]);

        return view('directory.show', [
            'member' => $user->load('providerPhotos'),
        ]);
    }
}
