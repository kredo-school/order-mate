<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    // 更新可能なカラムを指定
    protected $fillable = [
        'name',
        'store_id',
    ];

    // リレーション（Category belongsTo Store）
    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
