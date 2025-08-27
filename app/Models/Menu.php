<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'menu_category_id',
        'name',
        'price',
        'description',
    ];
    protected $table = 'menus';

    public function category()
    {
        return $this->belongsTo(Category::class, 'menu_category_id');
    }

    // app/Models/Menu.php
    public function customGroups()
    {
        return $this->belongsToMany(CustomGroup::class, 'menu_custom_groups')
            ->withPivot('is_required', 'max_selectable');
    }
}
