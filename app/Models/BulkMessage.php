<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BulkMessage extends Model
{
    protected $fillable = [
        'created_by',
        'channel',
        'subject',
        'body',
        'whatsapp_template_name',
        'whatsapp_template_params',
        'filters_snapshot',
        'status',
        'recipient_count',
        'sent_count',
        'failed_count',
    ];

    protected function casts(): array
    {
        return [
            'whatsapp_template_params' => 'array',
            'filters_snapshot' => 'array',
        ];
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function recipients()
    {
        return $this->hasMany(BulkMessageRecipient::class);
    }
}
