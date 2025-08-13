<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(){
        // フォルダー、ファイル名
        return view('managers.products.categories');
    }
}
