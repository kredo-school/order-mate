<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session as CheckoutSession;
use App\Models\Order;
use App\Models\Table;

class CheckoutController extends Controller
{
    public function checkout(Request $request, $storeName, $tableUuid)
    {
        $table = Table::where('uuid', $tableUuid)->firstOrFail();
    
        $order = $table->orders()
            ->where('status', 'open')
            ->latest()
            ->first();
    
        if (! $order) {
            // 次の人用に「注文開始画面」へ誘導
            return redirect()->route('guest.startOrder', [$storeName, $tableUuid])
                             ->with('info', 'No active order. Please start a new order.');
        }
    
        if ($order->is_paid && $order->payment_method === 'stripe') {
            $order->update(['status' => 'closed']);
    
            return view('guests.checkout-complete', [
                'table'     => $table,
                'order'     => $order,
                'message'   => 'Thank you for coming!',
                'showTotal' => false,
            ]);
        }
    
        return view('guests.checkout-complete', [
            'table'     => $table,
            'order'     => $order,
            'message'   => 'Thank you for coming, please proceed to the cashier.',
            'showTotal' => true,
        ]);
    }
    

    public function success(Request $request)
    {
        $sessionId = $request->get('session_id');
        if (!$sessionId) abort(404);
    
        // 必要なら Stripe からセッション情報を取得して、
        // ビューに渡すくらいに留める
        Stripe::setApiKey(config('services.stripe.secret'));
        $session = \Stripe\Checkout\Session::retrieve($sessionId);
    
        return view('guests.payment-complete', [
            'session' => $session,
        ]);
    }

    public function payment(Request $request, $storeName, $tableUuid)
    {
        $table = Table::where('uuid', $tableUuid)->firstOrFail();
        $order = $table->orders()
            ->where('status', 'open')
            ->latest()
            ->first();

        if (! $order) {
            return redirect()->back()->with('error', 'No active order found.');
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        $session = CheckoutSession::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'php',
                    'product_data' => [
                        'name' => "Order #{$order->id}",
                    ],
                    'unit_amount' => $order->total_price * 100, // セント単位
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('guest.checkout.success', [$storeName, $tableUuid]) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => url()->previous(),
            'metadata' => [
                'order_id' => $order->id,
            ],
        ]);

        return redirect($session->url);
    }

    public function payByManager(Request $request, $tableId)
    {
        $table = Table::findOrFail($tableId);
    
        $order = Order::where('table_id', $table->id)
            ->where('is_paid', false)
            ->latest()
            ->first();
    
        if (! $order) {
            return back()->with('error', 'No unpaid order found for this table.');
        }
    
        $order->update([
            'is_paid'        => true,
            'payment_method' => $request->input('payment_method', 'manual'),
            'payment_id'     => null,
        ]);
    
        return back()->with('success', 'Order marked as paid via '.$request->payment_method);
    }

    public function checkoutByManager(Request $request, Table $table)
    {
        $order = $table->orders()
            ->where('status', 'open') // ← pending じゃなくて open に揃えるのがよさそう
            ->latest()
            ->first();
    
        if (! $order) {
            return redirect()->route('manager.tables.show', $table->id)
                             ->with('error', 'No active order found.');
        }
    
        // Stripe支払い済みかどうか判定
        if ($order->is_paid) {
            $order->update(['status' => 'closed']);
    
            return redirect()->route('manager.tables', [
                'storeName' => $table->user->store->name,
                'tableUuid' => $table->uuid,
            ]);
        }

        return redirect()->route('manager.tables', [
            'storeName' => $table->user->store->name,
            'tableUuid' => $table->uuid,
        ]);
    }
}
