<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    protected $fillable = ['store_id', 'uuid', 'number'];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
