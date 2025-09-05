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

        $products = $initialCategory
            ? Menu::where('menu_category_id', $initialCategory->id)
                  ->where('user_id', $store->user_id)
                  ->with('customGroups.customOptions')
                  ->get()
            : collect();

        $menus = Menu::where('user_id', $store->user_id)->with('category')->get();

        // 重要: isGuestPage を明示的に渡す
        return view('guests.index', compact('store', 'table', 'menus', 'all_categories', 'products'))
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
}
