<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffCall extends Model
{
    protected $fillable = ['table_id', 'is_read'];

    public function table()
    {
        return $this->belongsTo(Table::class);
    }
}
