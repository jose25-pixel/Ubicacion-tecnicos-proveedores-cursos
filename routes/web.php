<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminCourseController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseCheckoutController;
use App\Http\Controllers\DirectoryController;
use App\Http\Controllers\PaypalWebhookController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TechnicianVerificationController;
use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $stats = [
        'technicians' => User::query()->where('role', 'technician')->count(),
        'providers' => User::query()->where('role', 'provider')->count(),
        'verified_technicians' => User::query()
            ->where('role', 'technician')
            ->whereNotNull('technician_verified_at')
            ->count(),
        'published_courses' => Course::query()->where('is_published', true)->count(),
    ];

    $featuredProviders = User::query()
        ->with('providerPhotos')
        ->where('role', 'provider')
        ->whereHas('providerPhotos')
        ->latest('updated_at')
        ->take(10)
        ->get();

    $featuredCourses = Course::query()
        ->where('is_published', true)
        ->latest('published_at')
        ->take(8)
        ->get();

    return view('home', compact('stats', 'featuredProviders', 'featuredCourses'));
});

Route::get('/cursos', [CourseController::class, 'index'])->name('courses.index');
Route::get('/cursos/{course:slug}', [CourseController::class, 'show'])->name('courses.show');

Route::get('/directorio', [DirectoryController::class, 'index'])->name('directory.index');
Route::get('/directorio/perfil/{user}', [DirectoryController::class, 'show'])
    ->name('directory.show');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])
        ->name('admin.dashboard');

    Route::get('/admin/cursos', [AdminCourseController::class, 'index'])
        ->name('admin.courses.index');
    Route::get('/admin/cursos/crear', [AdminCourseController::class, 'create'])
        ->name('admin.courses.create');
    Route::post('/admin/cursos', [AdminCourseController::class, 'store'])
        ->name('admin.courses.store');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/verificacion-tecnica', [TechnicianVerificationController::class, 'create'])
        ->name('technician.verification.create');
    Route::post('/verificacion-tecnica', [TechnicianVerificationController::class, 'store'])
        ->name('technician.verification.store');

    Route::post('/cursos/{course}/checkout/paypal', [CourseCheckoutController::class, 'createPaypalOrder'])
        ->name('courses.checkout.paypal.create');
    Route::get('/cursos/checkout/paypal/{order}/return', [CourseCheckoutController::class, 'paypalReturn'])
        ->name('courses.checkout.paypal.return');
    Route::get('/cursos/checkout/paypal/{order}/cancel', [CourseCheckoutController::class, 'paypalCancel'])
        ->name('courses.checkout.paypal.cancel');

    Route::get('/mis-cursos', [CourseController::class, 'myCourses'])
        ->name('courses.my');
});

Route::post('/webhooks/paypal', [PaypalWebhookController::class, 'handle'])
    ->withoutMiddleware([ValidateCsrfToken::class])
    ->name('webhooks.paypal');

require __DIR__.'/auth.php';
