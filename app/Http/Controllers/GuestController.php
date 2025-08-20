<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\Table;
use Illuminate\Http\Request;

class GuestController extends Controller
{
    public function index($storeName, $tableUuid)
    {
        // 店舗名から店舗を取得
        $store = Store::where('store_name', $storeName)->firstOrFail();

        // テーブルをUUIDで取得（store_idも一致することを確認）
        $table = Table::where('store_id', $store->id)
                      ->where('uuid', $tableUuid)
                      ->firstOrFail();

        // 店舗のメニューをカテゴリ別で取得
        $menus = $store->menus()->with('category')->get();

        return view('guests.index', compact('store', 'table', 'menus'));
    }
}
