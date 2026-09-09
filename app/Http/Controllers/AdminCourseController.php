<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminCourseController extends Controller
{
    public function index(): View
    {
        $this->ensureAdmin();

        $courses = Course::query()->latest('id')->paginate(20);

        return view('admin.courses.index', compact('courses'));
    }

    public function create(): View
    {
        $this->ensureAdmin();

        return view('admin.courses.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->ensureAdmin();

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:courses,slug'],
            'summary' => ['nullable', 'string', 'max:1000'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $slug = $validated['slug'] ?: Str::slug($validated['title']);
        if (Course::query()->where('slug', $slug)->exists()) {
            $slug = $slug.'-'.Str::random(4);
        }

        Course::create([
            'created_by' => auth()->id(),
            'title' => $validated['title'],
            'slug' => $slug,
            'summary' => $validated['summary'] ?? null,
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'currency' => strtoupper($validated['currency']),
            'is_published' => (bool) ($validated['is_published'] ?? false),
            'published_at' => ($validated['is_published'] ?? false) ? now() : null,
        ]);

        return redirect()->route('admin.courses.index')->with('status', 'Curso creado correctamente.');
    }

    private function ensureAdmin(): void
    {
        abort_unless(auth()->check() && auth()->user()->role === 'admin', 403);
    }
}
