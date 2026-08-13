<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatHistory extends Model
{
    protected $table = 'chat_histories';

    protected $fillable = [
        'session_id',
        'user_query',
        'bot_response',
        'matched_intent',
        'context_product_id',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array'
    ];
}
