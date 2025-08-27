<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $fillable = ['chat_type', 'store_id'];

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
