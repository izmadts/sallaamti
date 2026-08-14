<?php

// app/Models/QueryResponse.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\SupportQuery;
use App\Models\User;

class QueryResponse extends Model
{
    protected $fillable = ['support_query_id', 'responder_id', 'message', 'is_internal'];

    public function supportQuery() { return $this->belongsTo(SupportQuery::class, 'support_query_id'); }
    public function responder() { return $this->belongsTo(User::class, 'responder_id'); }
}