<?php
// app/Models/SupportQuery.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\QueryResponse;

class SupportQuery extends Model
{
    protected $fillable = [
        'user_id',
        'assigned_to',
        'category',
        'subject',
        'description',
        'status',
        'priority',
        'is_anonymous',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'assigned_to' => 'integer',
        'is_anonymous' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
    public function responses()
    {
        return $this->hasMany(QueryResponse::class);
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'new' => 'yellow',
            'assigned' => 'blue',
            'in_progress' => 'purple',
            'resolved' => 'green',
            'closed' => 'gray',
            default => 'gray',
        };
    }
}
