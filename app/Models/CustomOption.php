<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomOption extends Model
{
    protected $fillable = ['custom_group_id', 'name', 'extra_price'];

    public function group()
    {
        return $this->belongsTo(CustomGroup::class);
    }
}
