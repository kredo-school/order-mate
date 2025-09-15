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
        $store = Store::where('store_name', $storeName)->firstOrFail();
        $all_categories = Category::where('user_id', $store->user_id)->get();
        $initialCategory = $all_categories->first();
    
        $table = Table::where('user_id', $store->user_id)
                      ->where('uuid', $tableUuid)
                      ->firstOrFail();
    
        // 最新の注文を取得
        $latestOrder = $table->orders()->latest()->first();
    
        if (!$latestOrder || $latestOrder->status === 'closed') {
            // まだ注文がない or 直前の注文が閉じられている → 新しい来店扱い
            return view('guests.welcome', compact('store', 'table'))
                   ->with('isGuestPage', true);
        }
    
        // open order がある場合 → メニュー表示
        $products = $initialCategory
            ? Menu::where('menu_category_id', $initialCategory->id)
                  ->where('user_id', $store->user_id)
                  ->with('customGroups.customOptions')
                  ->get()
            : collect();
    
        $menus = Menu::where('user_id', $store->user_id)->with('category')->get();
    
        return view('guests.index', compact('store', 'table', 'menus', 'all_categories', 'products'))
               ->with('isGuestPage', true);
    }
    
    public function welcome($storeName, $tableUuid)
    {
        $store = Store::where('store_name', $storeName)->firstOrFail();
        $table = Table::where('user_id', $store->user_id)
                    ->where('uuid', $tableUuid)
                    ->firstOrFail();

        return view('guests.welcome', compact('store', 'table'))
            ->with('isGuestPage', true);
    }

    public function startOrder(Request $request, $storeName, $tableUuid)
    {
        $request->validate([
            'guest_count' => 'required|integer|min:1|max:20',
        ]);

        $store = Store::where('store_name', $storeName)->firstOrFail();
        $table = Table::where('user_id', $store->user_id)
                    ->where('uuid', $tableUuid)
                    ->firstOrFail();

        // 既存の open 注文があるなら再利用する
        $existingOrder = $table->orders()->where('status', 'open')->latest()->first();
        if ($existingOrder) {
            return redirect()->route('guest.index', [$storeName, $tableUuid]);
        }

        // 新しい注文を作成
        $table->orders()->create([
            'status'      => 'open',
            'guest_count' => $request->guest_count,
            'is_paid'     => false,
            'user_id'     => $table->user_id,
            'total_price' => 0,
            'order_type' => 'dine-in',
        ]);

        return redirect()->route('guest.index', [$storeName, $tableUuid]);
    }

    public function show($storeName, $tableUuid, $productId)
    {
        $store = Store::where('store_name', $storeName)->firstOrFail();
        $all_categories = Category::where('user_id', $store->user_id)->get();
        $table = Table::where('user_id', $store->user_id)
                      ->where('uuid', $tableUuid)
                      ->firstOrFail();
    
        $latestOrder = $table->orders()->latest()->first();
        if ($latestOrder && $latestOrder->status === 'closed') {
            return redirect()->route('guest.checkout.complete', [
                'storeName' => $store->store_name,
                'tableUuid' => $table->uuid,
            ])->with('error', 'This table has already checked out.');
        }
    
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
}
