<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $casts = [
        'is_read' => 'boolean',
    ];
    protected $fillable = ['chat_id', 'user_id', 'content', 'is_read'];

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUnread($query){
        return $query->where('is_read', false);
    }
}
