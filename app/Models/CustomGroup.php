<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomGroup extends Model
{
    protected $fillable = [
        'user_id',
        'title',
    ];

    public function options()
    {
        return $this->hasMany(CustomOption::class, 'custom_group_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
