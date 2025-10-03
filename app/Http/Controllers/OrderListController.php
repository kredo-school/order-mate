<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
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

    public function json(Request $request)
{
    // フロントから「最後に表示してる注文ID」を送らせて差分だけ返す
    $lastId = $request->input('last_id', 0);

    // 最新の未completedな注文アイテムを取得
    $orders = OrderItem::with(['order.table', 'menu.category'])
        ->where('id', '>', $lastId)
        ->whereHas('order', function ($q) {
            $q->where('status', '!=', 'closed');
        })
        ->orderBy('id', 'asc')
        ->get();

    // Blade で使ってる形式に揃える
    $rows = $orders->map(function ($item) {
        return [
            'id' => $item->id,
            'table' => $item->order->table->number ?? '',
            'item' => $item->menu->name ?? '',
            'option' => $item->custom_options_text ?? '',
            'quantity' => $item->quantity,
            'orderType' => $item->order->order_type,
            'orderId' => $item->order->id,
            'category' => $item->menu->category->name ?? '',
            'status' => $item->status,
            'updatedAt' => $item->updated_at->toIso8601String(),
        ];
    });

    return response()->json($rows);
}

}
