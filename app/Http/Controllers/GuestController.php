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
        $all_categories = Category::all();
        $initialCategory = $all_categories->first();
        // 店舗名から店舗を取得
        $store = Store::where('store_name', $storeName)->firstOrFail();

        // テーブルをUUIDで取得（store_idも一致することを確認）
        $table = Table::where('store_id', $store->id)
                      ->where('uuid', $tableUuid)
                      ->firstOrFail();

        // カテゴリが無い/メニューが無い場合でも必ずコレクションを渡す
        $products = $initialCategory
        ? Menu::where('menu_category_id', $initialCategory->id)->get()
        : collect();

        // 店舗のメニューをカテゴリ別で取得
        $menus = $store->menus()->with('category')->get();

        return view('guests.index', compact('store', 'table', 'menus', 'all_categories', 'products'));
    }
}
