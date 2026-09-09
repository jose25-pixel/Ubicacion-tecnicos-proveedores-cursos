<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name',
    'email',
    'password',
    'role',
    'specialty',
    'spare_parts_type',
    'country',
    'state',
    'years_experience',
    'phone',
    'whatsapp',
    'studies',
    'diplomas',
    'diploma_file_path',
    'address',
    'latitude',
    'longitude',
    'technician_verification_score',
    'technician_verification_attempts',
    'technician_verified_at',
    'technician_verification_locked_until',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'latitude' => 'float',
            'longitude' => 'float',
            'technician_verified_at' => 'datetime',
            'technician_verification_locked_until' => 'datetime',
        ];
    }

    public function isTechnicianVerified(): bool
    {
        return $this->role === 'technician' && $this->technician_verified_at !== null;
    }

    public function receivedProfileViews(): HasMany
    {
        return $this->hasMany(ProfileView::class, 'viewed_user_id');
    }

    public function providerPhotos(): HasMany
    {
        return $this->hasMany(ProviderPhoto::class)->orderBy('position');
    }

    public function createdCourses(): HasMany
    {
        return $this->hasMany(Course::class, 'created_by');
    }

    public function courseOrders(): HasMany
    {
        return $this->hasMany(CourseOrder::class);
    }

    public function courseEnrollments(): HasMany
    {
        return $this->hasMany(CourseEnrollment::class);
    }
}
