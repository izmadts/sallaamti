<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'gender',
        'phone',
        'email',
        'looking_for',
        'source',
        'status',
        'assigned_to',
        'notes',
        'next_follow_up_at',
        'nikah_profile_id',
        'package',
        'package_price',
        'package_started_at',
        'package_expires_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'next_follow_up_at' => 'date',
            'package_price' => 'decimal:2',
            'package_started_at' => 'date',
            'package_expires_at' => 'date',
        ];
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function nikahProfile()
    {
        return $this->belongsTo(NikahProfile::class);
    }

    public function shortlistItems()
    {
        return $this->hasMany(LeadShortlistItem::class);
    }

    public function isConverted(): bool
    {
        return $this->nikah_profile_id !== null;
    }
}
