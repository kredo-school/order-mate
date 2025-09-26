<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\CustomOption;
use App\Models\Menu;
use App\Models\OrderItem;
use App\Models\OrderItemCustomOption;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * カートを表示
     */
    public function show($storeName, $tableUuid)
    {
        $table = Table::where('uuid', $tableUuid)
            ->with('user.store')
            ->firstOrFail();

        $store = $table->user->store;

        $orderItems = OrderItem::whereHas('order', function ($query) use ($table) {
            $query->where('table_id', $table->id)
                ->where('status', 'open');
        })
            ->where('status', 'pending')
            ->with(['menu', 'customOptions.customOption'])
            ->orderBy('created_at')
            ->get();

        $subTotal = $orderItems->sum('price');

        $order = Order::where('table_id', $table->id)
            ->where('status', 'open')
            ->first();

        $totalPrice = $order?->total_price ?? 0;

        return view('guests.cart', compact('store', 'table', 'orderItems', 'subTotal', 'totalPrice'));
    }

    /**
     * 商品をカートに追加
     */
    public function add(Request $request, $storeName, $tableUuid, $menuId)
    {
        $menu     = Menu::findOrFail($menuId);
        $quantity = max(1, (int)$request->input('quantity', 1));
        $options  = $request->input('options', []);
        $table = Table::where('uuid', $tableUuid)->firstOrFail();

        // === テーブル番号が 0 なら takeout にする ===
        $orderType = ($table->number == 0) ? 'takeout' : 'dine-in';

        $order = Order::firstOrCreate(
            [
                'table_id' => $table->id,
                'status'   => 'open',
            ],
            [
                'user_id'     => $table->user_id,
                'total_price' => 0,
                'order_type'  => $orderType,
            ]
        );

        DB::transaction(function() use ($menu, $quantity, $options, $order) {

            $extraPriceMap = [];
            $optIds = array_keys($options);
            $opts = CustomOption::whereIn('id', $optIds)->get()->keyBy('id');

            foreach ($options as $optId => $optQty) {
                $extraPriceMap[$optId] = $opts[$optId]->extra_price ?? 0;
            }

            foreach ($options as $optId => $optQty) {
                for ($i = 0; $i < $optQty; $i++) {
                    $itemPrice = $menu->price + ($extraPriceMap[$optId] ?? 0);
                    $orderItem = OrderItem::create([
                        'order_id' => $order->id,
                        'menu_id'  => $menu->id,
                        'quantity' => 1,
                        'price'    => $itemPrice,
                        'status'   => 'pending',
                    ]);

                    OrderItemCustomOption::create([
                        'order_item_id'    => $orderItem->id,
                        'custom_option_id' => $optId,
                        'quantity'         => 1,
                        'extra_price'      => $extraPriceMap[$optId],
                    ]);
                }
            }

            $totalOptionsQty = array_sum($options);
            $remaining = $quantity - $totalOptionsQty;
            for ($i = 0; $i < $remaining; $i++) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_id'  => $menu->id,
                    'quantity' => 1,
                    'price'    => $menu->price,
                    'status'   => 'pending',
                ]);
            }

            $order->total_price = $order->orderItems()->sum('price');
            $order->save();
        });

        return redirect()->route('guest.cart.addComplete', [
            'storeName' => $storeName,
            'tableUuid' => $tableUuid,
        ]);
    }
    public function destroy($storeName, $tableUuid, OrderItem $orderItem)
    {
        $order = $orderItem->order;

        // order の total_price を減算
        $order->decrement('total_price', $orderItem->price);

        // 関連するカスタムオプション削除
        $orderItem->customOptions()->delete();

        // order_item 削除
        $orderItem->delete();

        return redirect()->route('guest.cart.show', [
            'storeName' => $storeName,
            'tableUuid' => $tableUuid,
        ])->with('success', 'Item removed from cart.');
    }

    public function edit($storeName, $tableUuid, OrderItem $orderItem)
    {
        // テーブル取得
        $table = Table::where('uuid', $tableUuid)
            ->with('user.store')
            ->firstOrFail();
        $store = $table->user->store;

        // 編集対象の商品（Menu）
        $product = $orderItem->menu;

        // 既存オプションの数量を取得
        $selectedOptions = $orderItem->customOptions
            ->pluck('quantity', 'custom_option_id')
            ->toArray();

        return view('guests.edit-cart', compact(
            'store',
            'table',
            'orderItem',
            'product',
            'selectedOptions'
        ));
    }

    public function update(Request $request, $storeName, $tableUuid, OrderItem $orderItem)
    {
        // === ここでステータスをチェック ===
        if ($orderItem->status !== 'pending') {
            return redirect()->route('guest.cart.show', [
                'storeName' => $storeName,
                'tableUuid' => $tableUuid,
            ])->with('error', 'This item can no longer be edited.');
        }

        $menu     = $orderItem->menu;
        $quantity = $request->input('quantity', 1);
        $options  = $request->input('options', []);

        $basePrice  = $menu->price * $quantity;
        $extraTotal = 0;

        // === 既存オプション削除して再登録 ===
        $orderItem->customOptions()->delete();

        foreach ($options as $optionId => $qty) {
            if ($qty > 0) {
                $option = CustomOption::findOrFail($optionId);
                $extraTotal += $option->extra_price * $qty;

                OrderItemCustomOption::create([
                    'order_item_id'    => $orderItem->id,
                    'custom_option_id' => $option->id,
                    'quantity'         => $qty,
                    'extra_price'      => $option->extra_price,
                ]);
            }
        }

        // === orderItem 更新 ===
        $orderItem->update([
            'quantity' => $quantity,
            'price'    => $basePrice + $extraTotal,
            // status は壊さずそのまま（基本 pending）
        ]);

        // === order の合計を再計算 ===
        $order = $orderItem->order;
        $order->total_price = $order->orderItems()->sum('price');
        $order->save();

        return redirect()->route('guest.cart.show', [
            'storeName' => $storeName,
            'tableUuid' => $tableUuid,
        ])->with('success', 'Cart updated.');
    }


    public function complete($storeName, $tableUuid)
    {
        $table = Table::where('uuid', $tableUuid)->firstOrFail();

        $order = Order::where('table_id', $table->id)

            // order_items のステータスを updating
            ->where('status', 'open')
            ->with('orderItems')
            ->firstOrFail();

        // pending のものだけ preparing に更新
        foreach ($order->orderItems as $item) {
            if ($item->status === 'pending') {
                $item->update(['status' => 'preparing']);
            }
        }

        // order 自体は open のまま
        return redirect()->route('guest.order.complete', [
            'storeName' => $storeName,
            'tableUuid' => $tableUuid,
        ]);
    }

    public function history($storeName, $tableUuid)
    {
        $table = Table::where('uuid', $tableUuid)
            ->with('user.store')
            ->firstOrFail();
        $store = $table->user->store;
        $orders = Order::where('table_id', $table->id)
            ->whereNotIn('status', ['pending', 'closed'])
            ->with(['orderItems.menu', 'orderItems.customOptions.customOption'])
            ->get();
        $history = collect();
        foreach ($orders as $order) {
            foreach ($order->orderItems as $item) {
                $menu = $item->menu;
                $status = $item->status;
                $orderedAt = $order->created_at;
                // カスタムオプションがある場合：オプションごとに行を作る（quantity＝オプション数量）
                if ($item->customOptions->isNotEmpty()) {
                    $sumOpts = (int) $item->customOptions->sum('quantity');
                    foreach ($item->customOptions as $opt) {
                        $perUnit = (float) ($menu->price + ($opt->extra_price ?? 0)); // 単価
                        $history->push([
                            'menu_name'  => $menu->name,
                            'options'    => $opt->customOption->name ?? '-',
                            'quantity'   => (int) $opt->quantity,
                            'price'      => $perUnit, // 単価
                            'status'     => $status,
                            'ordered_at' => $orderedAt,
                        ]);
                    }
                    // オプション無しの残り個数があれば行を追加（options = '-'）
                    $leftover = $item->quantity - $sumOpts;
                    if ($leftover > 0) {
                        $history->push([
                            'menu_name'  => $menu->name,
                            'options'    => '-',
                            'quantity'   => $leftover,
                            'price'      => (float) $menu->price,
                            'status'     => $status,
                            'ordered_at' => $orderedAt,
                        ]);
                    }
                } else {
                    // オプション無しのアイテム（quantity が複数ならまとめて1行）
                    // item->price は合計価格の可能性があるので単価は安全に menu->price を使うか、
                    // item->price が合計である前提なら単価 = item->price / quantity
                    $perUnit = $item->quantity > 0
                        ? (float) round($item->price / $item->quantity, 2)
                        : (float) $menu->price;
                    $history->push([
                        'menu_name'  => $menu->name,
                        'options'    => '-',
                        'quantity'   => (int) $item->quantity,
                        'price'      => $perUnit,
                        'status'     => $status,
                        'ordered_at' => $orderedAt,
                    ]);
                }
            }
        }
        // ゲスト向けビューは $history という変数名を期待しているのでそのまま渡す
        return view('guests.order-history', compact('store', 'table', 'history'));
    }

    public function toggleStatus(OrderItem $orderItem)
    {
        if ($orderItem->status === 'preparing') {
            $orderItem->update(['status' => 'ready']);
        } elseif ($orderItem->status === 'ready') {
            $orderItem->update(['status' => 'completed']);
        }
        return response()->json(['status' => $orderItem->status]);
    }


    public function historyByTable($tableId)
    {
        $table = Table::withCount([
            'orders as open_count' => function ($q) {
                $q->where('status', 'open');
            }
        ])->findOrFail($tableId);
        $orders = Order::with(['orderItems.menu', 'orderItems.customOptions.customOption'])
            ->where('table_id', $tableId)
            ->where('status', 'open')
            ->get();
        $history = collect();
        $totalPrice = 0;
        // 最新の order をチェック（支払い状態参照）
        $latestOrder   = $orders->sortByDesc('created_at')->first();
        $isPaid        = $latestOrder?->is_paid ?? false;
        $paymentMethod = $latestOrder?->payment_method ?? null;
        foreach ($orders as $order) {
            foreach ($order->orderItems as $item) {
                $menu      = $item->menu;
                $status    = $item->status;
                $orderedAt = $order->created_at;
                if ($item->customOptions->isNotEmpty()) {
                    $sumOpts = (int) $item->customOptions->sum('quantity');
                    foreach ($item->customOptions as $opt) {
                        $perUnit = (float) ($menu->price + ($opt->extra_price ?? 0));
                        $lineQty = (int) $opt->quantity;
                        $history->push([
                            'menu_name'  => $menu->name,
                            'options'    => $opt->customOption->name ?? '-',
                            'quantity'   => $lineQty,
                            'price'      => $perUnit,
                            'status'     => $status,
                            'ordered_at' => $orderedAt,
                        ]);
                        $totalPrice += $perUnit * $lineQty;
                    }
                    $leftover = $item->quantity - $sumOpts;
                    if ($leftover > 0) {
                        $perUnit = (float) $menu->price;
                        $history->push([
                            'menu_name'  => $menu->name,
                            'options'    => '-',
                            'quantity'   => $leftover,
                            'price'      => $perUnit,
                            'status'     => $status,
                            'ordered_at' => $orderedAt,
                        ]);
                        $totalPrice += $perUnit * $leftover;
                    }
                } else {
                    $perUnit = $item->quantity > 0
                        ? (float) round($item->price / $item->quantity, 2)
                        : (float) $menu->price;
                    $history->push([
                        'menu_name'  => $menu->name,
                        'options'    => '-',
                        'quantity'   => (int) $item->quantity,
                        'price'      => $perUnit,
                        'status'     => $status,
                        'ordered_at' => $orderedAt,
                    ]);
                    $totalPrice += $perUnit * (int)$item->quantity;
                }
            }
        }
        // btn-primary 切り替え用に open_count を渡す
        return view('managers.tables.show', compact(
            'table',
            'history',
            'totalPrice',
            'isPaid',
            'paymentMethod'
        ));
    }


    public function checkoutComplete($storeName, $tableUuid)
    {
        $table = Table::where('uuid', $tableUuid)->with('user.store')->firstOrFail();
        $store = $table->user->store;

        return view('guests.checkout-complete', compact('store', 'table'));
    }
}
