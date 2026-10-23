<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'password',
        'role',
        'poster_status',
        'organization',
        'organization_type',
        'position',
        'proof_url',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // $user->name returns "First Middle Last"
    public function getNameAttribute(): string
    {
        return collect([
            $this->first_name,
            $this->middle_name,
            $this->last_name,
        ])
            ->filter()
            ->implode(' ');
    }

    // Which home page this user lands on
    public function homeRoute(): string
    {
        return match ($this->role) {
            'admin'  => 'admin.home',
            'poster' => 'poster.home',
            default  => 'seeker.home',
        };
    }

    public function roleLabel(): string
    {
        return match ($this->role) {
            'admin'  => 'Admin',
            'poster' => 'Organization',
            default  => 'Seeker',
        };
    }

    // A user can have many listings
    public function listings(): HasMany
    {
        return $this->hasMany(Listing::class);
    }

    // A seeker has one profile (created on first save)
    public function seekerProfile(): HasOne
    {
        return $this->hasOne(SeekerProfile::class);
    }

    // A seeker can send many applications
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }
}
