<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseEnrollment;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(): View
    {
        $courses = Course::query()
            ->where('is_published', true)
            ->withCount(['lessons' => fn ($q) => $q->where('is_published', true)])
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(12);

        return view('courses.index', compact('courses'));
    }

    public function show(Request $request, Course $course): View
    {
        abort_unless($course->is_published, 404);

        $course->load(['lessons' => fn ($q) => $q->where('is_published', true)->orderBy('sort_order')]);

        $course->lessons->transform(function ($lesson) {
            $lesson->setAttribute('embed_id', $this->extractYoutubeId((string) $lesson->youtube_url));

            return $lesson;
        });

        $hasAccess = (float) $course->price <= 0;
        if ($request->user()) {
            $hasAccess = $hasAccess || CourseEnrollment::query()
                ->where('user_id', $request->user()->id)
                ->where('course_id', $course->id)
                ->where('status', 'active')
                ->exists();
        }

        return view('courses.show', [
            'course' => $course,
            'hasAccess' => $hasAccess,
        ]);
    }

    public function myCourses(Request $request): View
    {
        $enrollments = CourseEnrollment::query()
            ->with(['course', 'lastWatchedLesson'])
            ->where('user_id', $request->user()->id)
            ->where('status', 'active')
            ->latest('started_at')
            ->latest('id')
            ->get();

        return view('courses.my-courses', compact('enrollments'));
    }

    private function extractYoutubeId(string $url): ?string
    {
        if ($url === '') {
            return null;
        }

        $patterns = [
            '/youtu\.be\/([A-Za-z0-9_-]{11})/i',
            '/youtube\.com\/watch\?v=([A-Za-z0-9_-]{11})/i',
            '/youtube\.com\/embed\/([A-Za-z0-9_-]{11})/i',
            '/youtube\.com\/shorts\/([A-Za-z0-9_-]{11})/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches) === 1) {
                return $matches[1];
            }
        }

        return null;
    }
}
