<?php

namespace App\Http\Controllers;

use App\Models\ProfileView;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()?->role === 'admin', 403);

        $totalUsers = User::count();
        $totalRegularUsers = User::where('role', 'user')->count();
        $totalTechnicians = User::where('role', 'technician')->count();
        $totalProviders = User::where('role', 'provider')->count();

        $usersByCountry = User::query()
            ->selectRaw("COALESCE(NULLIF(country, ''), 'Sin pais') as country")
            ->selectRaw("SUM(CASE WHEN role = 'user' THEN 1 ELSE 0 END) as users_count")
            ->selectRaw("SUM(CASE WHEN role = 'technician' THEN 1 ELSE 0 END) as technicians_count")
            ->selectRaw("SUM(CASE WHEN role = 'provider' THEN 1 ELSE 0 END) as providers_count")
            ->groupBy('country')
            ->orderByDesc(DB::raw('users_count + technicians_count + providers_count'))
            ->get();

        $mostViewedProfiles = User::query()
            ->leftJoin('profile_views', 'users.id', '=', 'profile_views.viewed_user_id')
            ->whereIn('users.role', ['technician', 'provider'])
            ->select('users.id', 'users.name', 'users.role', 'users.country', 'users.state')
            ->selectRaw('COUNT(profile_views.id) as total_views')
            ->groupBy('users.id', 'users.name', 'users.role', 'users.country', 'users.state')
            ->orderByDesc('total_views')
            ->limit(10)
            ->get();

        $viewsLast7Days = ProfileView::query()
            ->selectRaw('DATE(created_at) as view_day')
            ->selectRaw('COUNT(*) as total_views')
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->groupBy('view_day')
            ->orderBy('view_day')
            ->get();

        return view('admin.dashboard', [
            'totalUsers' => $totalUsers,
            'totalRegularUsers' => $totalRegularUsers,
            'totalTechnicians' => $totalTechnicians,
            'totalProviders' => $totalProviders,
            'usersByCountry' => $usersByCountry,
            'mostViewedProfiles' => $mostViewedProfiles,
            'viewsLast7Days' => $viewsLast7Days,
        ]);
    }
}
