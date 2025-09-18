<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use App\Models\Order;

class StripeWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $payload   = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret    = env('STRIPE_WEBHOOK_SECRET');

        if (empty($sigHeader) || empty($secret)) {
            Log::warning('Stripe webhook: missing signature header or secret.');
            return response('Missing headers', 400);
        }

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (SignatureVerificationException $e) {
            Log::warning('Stripe webhook: invalid signature. '.$e->getMessage());
            return response('Invalid signature', 400);
        } catch (\UnexpectedValueException $e) {
            Log::warning('Stripe webhook: invalid payload. '.$e->getMessage());
            return response('Invalid payload', 400);
        }

        Log::info('Stripe webhook received: '.$event->type, ['id' => $event->id]);

        switch ($event->type) {
            case 'checkout.session.completed':
                $session = $event->data->object;

                $orderId = $session->metadata->order_id ?? null;
                $paymentIntent = $session->payment_intent ?? null;

                if ($orderId) {
                    $order = Order::find($orderId);
                    if ($order && !$order->is_paid) {
                        $order->update([
                            'is_paid'       => true,
                            'payment_method'=> 'stripe',
                            'payment_id'    => $paymentIntent,
                        ]);
                        Log::info("Order #{$orderId} closed and marked as paid.");
                    }
                } else {
                    Log::warning('checkout.session.completed without order_id');
                }
                break;

            case 'payment_intent.succeeded':
                $pi = $event->data->object;
                Log::info('payment_intent.succeeded', [
                    'id'       => $pi->id,
                    'amount'   => $pi->amount,
                    'currency' => $pi->currency,
                    'status'   => $pi->status,
                ]);
                break;
        }

        return response('Webhook handled', 200);
    }
}
