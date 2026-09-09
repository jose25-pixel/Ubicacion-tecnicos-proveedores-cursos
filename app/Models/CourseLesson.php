<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseLesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'slug',
        'youtube_url',
        'duration_seconds',
        'sort_order',
        'is_preview',
        'is_published',
    ];

    protected $casts = [
        'duration_seconds' => 'integer',
        'sort_order' => 'integer',
        'is_preview' => 'boolean',
        'is_published' => 'boolean',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function enrollmentsAsLastWatched(): HasMany
    {
        return $this->hasMany(CourseEnrollment::class, 'last_watched_lesson_id');
    }
}
