<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Store;
use App\Models\Table;
use App\Models\Category;
use Illuminate\Http\Request;

class GuestController extends Controller
{
    public function index(Request $request, $storeName, $tableUuid)
    {
        $store = Store::where('store_name', $storeName)->firstOrFail();

        $all_categories = Category::where('user_id', $store->user_id)->get();
        $initialCategory = $all_categories->first();

        $table = Table::where('user_id', $store->user_id)
            ->where('uuid', $tableUuid)
            ->firstOrFail();

        // 基本のクエリ
        $query = Menu::where('user_id', $store->user_id)
            ->with('customGroups.customOptions');

        // 🔍 検索ワードがある場合
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%");
            });
        } else {
            // 検索が無いときは初期カテゴリのみ
            if ($initialCategory) {
                $query->where('menu_category_id', $initialCategory->id);
            } else {
                $query->whereRaw('1=0'); // データなし
            }
        }

        $products = $query->get();

        // ここでカート数量を取得
        $cart = session()->get("cart_{$table->uuid}", []);
        $cartCount = array_sum(array_column($cart, 'quantity'));

        return view('guests.index', compact('store', 'table', 'all_categories', 'products'))
            ->with('isGuestPage', true);
    }

    public function show($storeName, $tableUuid, $productId)
    {
        $store = Store::where('store_name', $storeName)->firstOrFail();
        $all_categories = Category::where('user_id', $store->user_id)->get();
        $table = Table::where('user_id', $store->user_id)
            ->where('uuid', $tableUuid)
            ->firstOrFail();

        $product = Menu::where('id', $productId)
            ->where('user_id', $store->user_id)
            ->with('customGroups.customOptions')
            ->firstOrFail();

        return view('guests.show', compact('store', 'table', 'all_categories', 'product'))
            ->with('isGuestPage', true);
    }

    public function call($storeName, $tableUuid)
    {
        $store = Store::where('store_name', $storeName)->firstOrFail();
        $table = Table::where('user_id', $store->user_id)
            ->where('uuid', $tableUuid)
            ->firstOrFail();

        return view('guests.call', compact('store', 'table'))
            ->with('isGuestPage', true);
    }


    public function byCategory($storeName, $tableUuid, $categoryId)
    {
        $store = Store::where('store_name', $storeName)->firstOrFail();
        $table = Table::where('uuid', $tableUuid)->firstOrFail();

        $products = Menu::where('menu_category_id', $categoryId)
            ->where('user_id', $store->user_id)
            ->get();

        return view('managers.products.partials.products', compact('products'))
            ->with('isGuestPage', true)
            ->with(compact('store', 'table'));
    }

    public function cartCount($storeName, $tableUuid)
{
    $table = Table::where('uuid', $tableUuid)->firstOrFail();
    $cart = session()->get("cart_{$table->uuid}", []);
    $totalItems = array_sum(array_column($cart, 'quantity'));

    return response()->json(['totalItems' => $totalItems]);
}
}
