<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
        // 更新可能なカラム
        protected $fillable = [
            'user_id',
            'password',
            'store_name',
            'store_url',
            'address',
            'phone',
            'store_photo',
            'open_hours',
        ];
    
        /**
         * Store に紐づくカテゴリ
         * 1つの店舗が複数のカテゴリを持つ
         */
        public function categories()
        {
            return $this->hasMany(Category::class);
        }
    
        /**
         * Store を所有しているユーザー
         * 1店舗は1人のユーザーに属する
         */
        public function user()
        {
            return $this->belongsTo(User::class);
        }
}
