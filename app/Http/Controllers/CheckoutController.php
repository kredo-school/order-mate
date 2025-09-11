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
        // 1) テーブル & 注文取得（サーバ側で金額を決める）
        $table = Table::where('uuid', $tableUuid)->firstOrFail();

        $order = Order::where('table_id', $table->id)
                      ->where('status', 'open')  // あなたのアプリの「まとめ注文」のステータス
                      ->firstOrFail();

        // 2) amount: Stripeは「最小通貨単位」で受け取る（PHPなら centavos -> *100）
        //    （Order->total_price が decimal で『PHP』通貨を使っている前提）
        $amount = (int) round($order->total_price * 100); // 100 = cents

        Stripe::setApiKey(config('services.stripe.secret'));

        $session = CheckoutSession::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'php', // 通貨。あなたのアプリの国に合わせて。
                    'product_data' => [
                        'name' => 'Table ' . $table->id . ' Order #' . $order->id,
                    ],
                    'unit_amount' => $amount,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('guest.checkout.success', [
                'storeName' => $order->table->user->store->store_name,
                'tableUuid' => $order->table->uuid,
            ]) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => url()->previous(),
            'metadata' => [
                'order_id' => $order->id,
            ],
        ]);

        // リダイレクトして Stripe Checkout に飛ばす
        return redirect($session->url);
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
}
