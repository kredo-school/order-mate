<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\CustomOption;
use App\Models\Menu;
use App\Models\OrderItem;
use App\Models\OrderItemCustomOption;
use App\Models\Table;
use Illuminate\Http\Request;

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

        $order = Order::where('table_id', $table->id)
            ->where('status', 'pending')
            ->with(['orderItems.menu', 'orderItems.customOptions.customOption'])
            ->first();

        return view('guests.cart', compact('store', 'table', 'order'));
    }

    /**
     * 商品をカートに追加
     */
    public function add(Request $request, $storeName, $tableUuid, $menuId)
    {
        $menu     = Menu::findOrFail($menuId);
        $quantity = $request->input('quantity', 1);
        $basePrice = $menu->price * $quantity;
        $options   = $request->input('options', []);

        // === 1. table を取得 ===
        $table = Table::where('uuid', $tableUuid)->firstOrFail();

        // === 2. pending の order を取得 or 作成 ===
        $order = Order::firstOrCreate(
            [
                'table_id' => $table->id,
                'status'   => 'pending',
            ],
            [
                'user_id'     => $table->user_id,
                'total_price' => 0,
                'order_type'  => 'dine-in',
            ]
        );

        // === 3. order_items を作成 ===
        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'menu_id'  => $menu->id,
            'quantity' => $quantity,
            'price'    => $basePrice,
            'status'   => 'pending',
        ]);

        $extraTotal = 0;

        // === 4. カスタムオプション ===
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

        // === 5. order_item の価格更新 ===
        $orderItem->update([
            'price' => $basePrice + $extraTotal,
        ]);

        // === 6. order の total_price を更新 ===
        $order->increment('total_price', $basePrice + $extraTotal);

        // === 7. 完了画面へ遷移 ===
        // JSONレスポンスに修正 ????????
        $totalItems = $order->orderItems->sum('quantity');

        return response()->json([
            'status' => 'success',
            'message' => '商品がカートに追加されました！',
            'totalItems' => $totalItems
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
        $table = Table::where('uuid', $tableUuid)->with('user.store')->firstOrFail();
        $store = $table->user->store;
        $product = $orderItem->menu;

        // 既存オプションの数量を取得
        $selectedOptions = $orderItem->customOptions->pluck('quantity', 'custom_option_id')->toArray();

        return view('guests.edit-cart', compact('store', 'table', 'orderItem', 'product', 'selectedOptions'));
    }

    public function update(Request $request, $storeName, $tableUuid, OrderItem $orderItem)
    {
        $menu     = $orderItem->menu;
        $quantity = $request->input('quantity', 1);
        $options  = $request->input('options', []);

        $basePrice = $menu->price * $quantity;
        $extraTotal = 0;

        // 既存オプション削除して再登録
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

        // orderItem 更新
        $orderItem->update([
            'quantity' => $quantity,
            'price'    => $basePrice + $extraTotal,
        ]);

        // order の合計を再計算
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
            ->where('status', 'pending')
            ->with('orderItems')
            ->firstOrFail();

        // order_items のステータスを updating
        foreach ($order->orderItems as $item) {
            $item->update(['status' => 'preparing']);
        }

        // order 自体も preparing にする場合はこちら
        $order->update(['status' => 'preparing']);

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
            ->where('status', '!=', 'pending')
            ->with(['orderItems.menu', 'orderItems.customOptions.customOption'])
            ->get();

        $history = [];

        foreach ($orders as $order) {
            foreach ($order->orderItems as $item) {

                // オプションがある場合は 1 件ずつ分割
                if ($item->customOptions->isNotEmpty()) {
                    foreach ($item->customOptions as $opt) {
                        $history[] = [
                            'menu_name' => $item->menu->name,
                            'options'   => $opt->customOption->name,
                            'quantity'  => 1, // オプション1つにつき1行にする
                            'price'     => $item->menu->price + ($opt->extra_price * $opt->quantity),
                            'status'    => $item->status,
                            'ordered_at' => $order->created_at,
                        ];
                    }
                } else {
                    // オプションなしの場合
                    $history[] = [
                        'menu_name' => $item->menu->name,
                        'options'   => '-',
                        'quantity'  => $item->quantity,
                        'price'     => $item->price,
                        'status'    => $item->status,
                        'ordered_at' => $order->created_at,
                    ];
                }
            }
        }

        $history = collect($history);

        return view('guests.order-history', compact('store', 'table', 'history'));
    }

    /**
     * カート内の合計アイテム数を取得
     */
    public function getCartCount(Request $request, $storeName, $tableUuid)
    {
        // テーブルのUUIDからテーブルIDを検索
        $table = Table::where('uuid', $tableUuid)->firstOrFail();

        // 進行中の注文（カート）を取得
        $order = Order::where('table_id', $table->id)
            ->where('status', 'pending')
            ->first();

        // 注文が存在すれば、注文アイテムの合計数量を計算
        $totalItems = $order ? $order->orderItems->sum('quantity') : 0;

        return response()->json(['totalItems' => $totalItems]);
    }
}
