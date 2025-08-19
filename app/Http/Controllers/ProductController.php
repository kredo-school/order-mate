<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Menu;
use App\Models\CustomGroup;
use Illuminate\Http\Request;

class ProductController extends Controller{
    public function index(){
        $all_categories = Category::all();
        $initialCategory = $all_categories->first();

        // カテゴリが無い/メニューが無い場合でも必ずコレクションを渡す
        $products = $initialCategory
            ? Menu::where('menu_category_id', $initialCategory->id)->get()
            : collect();

        return view('managers.products.products', compact('all_categories', 'products'));
    }

    public function byCategory($id){
        $products = Menu::where('menu_category_id', $id)->get(); // 0件でもコレクション
        return view('managers.products.partials.products', compact('products'));
    }

    public function create(){
        $all_categories = Category::all();
        $customGroups = CustomGroup::all(); 
        // フォルダー、ファイル名
        return view('managers.products.add-product')->with([
            'all_categories' => $all_categories,
            'customGroups' => $customGroups,
        ]);
    }

    // store

    // update

}
