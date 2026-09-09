<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfileView extends Model
{
    use HasFactory;

    protected $fillable = [
        'viewed_user_id',
        'viewer_user_id',
        'viewer_ip',
    ];

    public function viewedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'viewed_user_id');
    }

    public function viewerUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'viewer_user_id');
    }
}
