<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    protected $fillable = [
        'donation_number',
        'user_id',
        'donor_name',
        'email',
        'phone',
        'amount',
        'purpose',
        'message',
        'is_anonymous',
        'payment_method',
        'payment_reference',
        'payment_screenshot',
        'payment_status',
        'payment_rejection_reason',
        'payment_confirmed_at',
    ];

    protected function casts(): array
    {
        return [
            'is_anonymous' => 'boolean',
            'payment_confirmed_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function generateNumber(): string
    {
        return 'DON-' . now()->format('Y') . '-' . strtoupper(uniqid());
    }
}
