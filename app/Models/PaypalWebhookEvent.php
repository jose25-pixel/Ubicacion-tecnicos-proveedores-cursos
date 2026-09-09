<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaypalWebhookEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'event_type',
        'resource_type',
        'course_order_id',
        'transmission_id',
        'transmission_time',
        'is_signature_valid',
        'processed_at',
        'payload',
    ];

    protected $casts = [
        'is_signature_valid' => 'boolean',
        'processed_at' => 'datetime',
        'payload' => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(CourseOrder::class, 'course_order_id');
    }
}
