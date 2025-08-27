<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

<<<<<<< HEAD
class Menu extends Model
{
=======
class Menu extends Model{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'menu_category_id',
        'name',
        'price',
        'description',
    ];
>>>>>>> 9b0db3f2b9b7a7f7c6f514460ba7e44a5234c217
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
