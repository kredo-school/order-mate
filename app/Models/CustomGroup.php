<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomGroup extends Model
{
    protected $fillable = [
        'store_id',
        'title',
    ];

    public function options()
    {
        return $this->hasMany(CustomOption::class);
    }
}
