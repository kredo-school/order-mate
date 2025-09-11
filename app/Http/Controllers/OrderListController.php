<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderListController extends Controller
{
    public function index()
    {
        $manager = Auth::user();

        // manager が持つ store_id を取得
        $store = $manager->store;

        $orders = Order::whereHas('table.user.store', function ($query) use ($store) {
            $query->where('id', $store->id);
        })
        ->with([
            'table',
            'orderItems' => function ($query) {
                // completed も含める
                $query->whereIn('status', ['preparing', 'ready', 'completed']);
            },
            'orderItems.menu.category',
            'orderItems.customOptions.customOption'
        ])
        ->orderBy('created_at', 'asc')
        ->get();

        return view('managers.order-lists.order-list', compact('orders'));
    }
}
