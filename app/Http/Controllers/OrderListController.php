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
        $store = $manager->store;

        if (! $store) {
            $orderRows = collect();
            return view('managers.order-lists.order-list', compact('orderRows'));
        }

        $orders = Order::whereHas('table.user.store', function ($q) use ($store) {
                $q->where('id', $store->id);
            })
            ->with([
                'table',
                'orderItems' => function ($q) {
                    $q->whereIn('status', ['preparing', 'ready', 'completed']);
                },
                'orderItems.menu.category',
                'orderItems.customOptions.customOption'
            ])
            ->orderBy('created_at', 'asc')
            ->get();

        // Flatten -> optionごとに行を分ける
        $orderRows = collect();
        foreach ($orders as $order) {
            foreach ($order->orderItems as $item) {
                $menuName = $item->menu->name;
                $orderType = $order->order_type;
                $category = $item->menu->category->name ?? '-';
                $timeLabel = $order->created_at->diffForHumans(null, true); // ex: "14m"

                if ($item->customOptions->isNotEmpty()) {
                    foreach ($item->customOptions as $opt) {
                        $orderRows->push([
                            'id'        => $item->id,
                            'orderId'   => $order->id,
                            'table'     => $order->table->number,
                            'time'      => $timeLabel,
                            'updatedAt' => $item->updated_at->toIso8601String(),
                            'item'      => $menuName,
                            'option'    => $opt->customOption->name ?? '-',
                            'quantity'  => (int) $opt->quantity,
                            'orderType' => $orderType,
                            'category'  => $category,
                            'status'    => $item->status,
                        ]);
                    }
                    // 残り（オプション無し）の数量があれば '-' 行を追加
                    $sumOpts = (int) $item->customOptions->sum('quantity');
                    $leftover = $item->quantity - $sumOpts;
                    if ($leftover > 0) {
                        $orderRows->push([
                            'id'        => $item->id,
                            'orderId'   => $order->id,
                            'table'     => $order->table->number,
                            'time'      => $timeLabel,
                            'updatedAt' => $item->updated_at->toIso8601String(),
                            'item'      => $menuName,
                            'option'    => '-',
                            'quantity'  => $leftover,
                            'orderType' => $orderType,
                            'category'  => $category,
                            'status'    => $item->status,
                        ]);
                    }
                } else {
                    $orderRows->push([
                        'id'        => $item->id,
                        'orderId'   => $order->id,
                        'table'     => $order->table->number,
                        'time'      => $timeLabel,
                        'updatedAt' => $item->updated_at->toIso8601String(),
                        'item'      => $menuName,
                        'option'    => '-',
                        'quantity'  => $item->quantity,
                        'orderType' => $orderType,
                        'category'  => $category,
                        'status'    => $item->status,
                    ]);
                }
            }
        }

        return view('managers.order-lists.order-list', compact('orderRows'));
    }
}
