<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    protected $fillable = ['user_id', 'uuid', 'number'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
