<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model{
    protected $table = 'menus';

    public function category(){
        return $this->belongsTo(Category::class, 'menu_category_id');
    }

}
