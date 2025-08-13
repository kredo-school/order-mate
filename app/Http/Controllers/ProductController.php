<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductController extends Controller{
    public function index(){
        // フォルダー、ファイル名
        return view('managers.products.products');
    }

    public function create(){
        // フォルダー、ファイル名
        return view('managers.products.add-product');
    }
}
