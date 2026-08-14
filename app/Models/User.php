<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'gender',
        'city',
        'avatar',
        'password',
        'provider',
        'provider_id',
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
    public function nikahProfile()
    {
        return $this->hasOne(NikahProfile::class);
    }
    public function avatarUrl(): string
    {
        return $this->avatar
            ? route('user.avatar', $this)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=0D8ABC&color=fff';
    }
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function donations()
    {
        return $this->hasMany(Donation::class);
    }
}
