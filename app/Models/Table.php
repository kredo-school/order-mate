<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    protected $fillable = ['user_id', 'uuid', 'number', 'is_active'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 有効なテーブルだけ取得するスコープ
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // 指定ストア（user_id）だけ
    public function scopeForStore($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // public function loadCount(){
    //     return $
    // }
}
