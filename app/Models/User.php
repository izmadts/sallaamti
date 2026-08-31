<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'username',
        'public_bio',
        'email',
        'phone',
        'gender',
        'city',
        'avatar',
        'password',
        'pin',
        'provider',
        'provider_id',
        'nikah_module_enabled',
        'quran_module_enabled',
        'counseling_module_enabled',
        'skills_module_enabled',
        'current_streak',
        'longest_streak',
        'last_active_date',
        'whatsapp_notify_opt_in',
        'newsletter_opt_out',
        'counselor_bio',
        'teacher_vetting_status',
        'teacher_vetting_notes',
        'teacher_vetted_at',
    ];

    protected $hidden = [
        'password',
        'pin',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'email_verified_at' => 'datetime',
            'deactivated_at' => 'datetime',
            'teacher_vetted_at' => 'datetime',
            'last_active_date' => 'date',
            'password' => 'hashed',
            'pin' => 'hashed',
            'nikah_module_enabled' => 'boolean',
            'quran_module_enabled' => 'boolean',
            'counseling_module_enabled' => 'boolean',
            'skills_module_enabled' => 'boolean',
            'whatsapp_notify_opt_in' => 'boolean',
        ];
    }

    public function trustedDevices()
    {
        return $this->hasMany(TrustedDevice::class);
    }

    public function isDeactivated(): bool
    {
        return $this->deactivated_at !== null;
    }

    public function isApprovedTeacher(): bool
    {
        return $this->teacher_vetting_status === 'approved';
    }

    public function deactivate(): void
    {
        $this->forceFill(['deactivated_at' => now()])->save();

        $this->nikahProfile()->update(['is_active' => false]);
    }

    public function reactivate(): void
    {
        $this->forceFill(['deactivated_at' => null])->save();

        $this->nikahProfile()->update(['is_active' => true]);
    }

    public function nikahProfile()
    {
        return $this->hasOne(NikahProfile::class);
    }

    public function pushSubscriptions()
    {
        return $this->hasMany(PushSubscription::class);
    }

    public function duaRequests()
    {
        return $this->hasMany(DuaRequest::class);
    }
    public function avatarUrl(): string
    {
        return $this->avatar
            ? route('user.avatar', $this)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=0D8ABC&color=fff';
    }

    // Same fallback as avatarUrl(), but pointed at the Sanctum-authenticated
    // Api\V1\AvatarController route instead of the web (session-auth,
    // owner/admin-only) one — see that controller's docblock for why a
    // separate, more permissive route exists for mobile.
    public function apiAvatarUrl(): string
    {
        return $this->avatar
            ? route('api.v1.avatar', $this)
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

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    // Posts need a stable, URL-safe handle for the author's public byline
    // link, but accounts are created without ever choosing one (registration
    // only asks for name/identifier) — generate it lazily, the first time
    // it's actually needed (submitting a post), rather than backfilling
    // every existing account up front for a feature most won't use.
    public function ensureUsername(): string
    {
        if ($this->username) {
            return $this->username;
        }

        $base = \Illuminate\Support\Str::slug($this->name) ?: 'member';
        $candidate = $base;
        $suffix = 1;

        while (static::where('username', $candidate)->where('id', '!=', $this->id)->exists()) {
            $candidate = $base . '-' . (++$suffix);
        }

        $this->forceFill(['username' => $candidate])->save();

        return $candidate;
    }

    public function counselingBookings()
    {
        return $this->hasMany(CounselingBooking::class, 'member_id');
    }

    // How this account was created — surfaced to admins so they can tell
    // Google/Facebook/WhatsApp-OTP/direct-password signups apart. Accounts
    // created before this was tracked have no `provider` value.
    public function joinSourceLabel(): string
    {
        return match ($this->provider) {
            'google' => '🔵 Google',
            'facebook' => '🔷 Facebook',
            'whatsapp' => '💬 WhatsApp OTP',
            'password' => '📧 Email/Password',
            default => '❓ Unknown',
        };
    }
}
