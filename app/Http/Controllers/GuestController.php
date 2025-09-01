<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Store;
use App\Models\Table;
use App\Models\Category;
use Illuminate\Http\Request;

class GuestController extends Controller
{
    public function index($storeName, $tableUuid)
    {
        // 店舗を取得
        $store = Store::where('store_name', $storeName)->firstOrFail();

        // カテゴリもその店舗のみに限定
        $all_categories = Category::where('user_id', $store->user_id)->get();
        $initialCategory = $all_categories->first();

        // テーブル取得
        $table = Table::where('user_id', $store->id)
                      ->where('uuid', $tableUuid)
                      ->firstOrFail();

        // 初期カテゴリの商品（その店舗限定）
        $products = $initialCategory
            ? Menu::where('menu_category_id', $initialCategory->id)
                  ->where('user_id', $store->user_id)
                  ->get()
            : collect();

        // その店舗の全メニュー（カテゴリ付き）
        $menus = Menu::where('user_id', $store->user_id)->with('category')->get();

        return view('guests.index', compact('store', 'table', 'menus', 'all_categories', 'products'));
    }

    public function call($storeName, $tableUuid)
    {
        $store = Store::where('store_name', $storeName)->firstOrFail();
        $table = Table::where('user_id', $store->id)
                    ->where('uuid', $tableUuid)
                    ->firstOrFail();

        return view('guests.call', compact('store', 'table'));
    }

}
