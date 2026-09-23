<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Webhook;

class StripeController extends Controller
{
    public function createSession(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
        ]);

        $order = Order::findOrFail($request->order_id);

        Stripe::setApiKey(config('services.stripe.secret'));

        $lineItems = collect($order->items)->map(function ($item) {
            return [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $item['product']['title'],
                        'images' => [$item['product']['image']],
                    ],
                    'unit_amount' => $item['product']['price'] * 100,
                ],
                'quantity' => $item['quantity'],
            ];
        })->toArray();

        if ($order->delivery_cost > 0) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => 'Delivery',
                        'description' => 'Shipping cost',
                    ],
                    'unit_amount' => (int) round($order->delivery_cost * 100),
                ],
                'quantity' => 1,
            ];
        }

        // Create a basic Stripe customer (required for customer_update)
        $customerData = [];

        if ($order->email) {
            $customerData['email'] = $order->email;
        }

        if ($order->name) {
            $customerData['name'] = $order->name;
        }

        try {
            $customer = \Stripe\Customer::create($customerData);
            $customerId = $customer->id;
        } catch (\Exception $e) {
            \Log::error('Stripe customer creation failed: ' . $e->getMessage());
            $customerId = null;
        }

        $sessionData = [
            //'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'automatic_tax' => [
                'enabled' => true,
            ],
            'billing_address_collection' => 'required',
            'shipping_address_collection' => [
                'allowed_countries' => ['AT', 'BE', 'BG', 'HR', 'CY', 'CZ', 'DK', 'EE', 'FI', 'FR', 'DE', 'GR', 'HU', 'IE', 'IT', 'LV', 'LT', 'LU', 'MT', 'NL', 'PL', 'PT', 'RO', 'SK', 'SI', 'ES', 'SE', 'GB', 'US', 'CA', 'AU', 'NZ', 'CH', 'NO', 'IS'],
            ],
            'success_url' => route('payment.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('payment.cancel') . '?order=' . $order->order_number,
            'client_reference_id' => $order->id,
            'metadata' => [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
            ],
        ];

        // Only add customer and customer_update if customer was created successfully
        if ($customerId) {
            $sessionData['customer'] = $customerId;
            $sessionData['customer_update'] = [
                'address' => 'auto',
                'shipping' => 'auto',
            ];
        } else if ($order->email) {
            $sessionData['customer_email'] = $order->email;
        }

        $session = Session::create($sessionData);

        Payment::create([
            'order_id' => $order->id,
            'amount' => (int) round($order->total_amount * 100),
            'currency' => 'eur',
            'provider' => 'stripe',
            'provider_payment_id' => $session->payment_intent,
            'status' => 'pending',
        ]);

        return redirect($session->url);
    }

    public function success(Request $request)
    {
        if (!$request->has('session_id')) {
            return redirect()->route('shop.index');
        }

        Stripe::setApiKey(config('services.stripe.secret'));
        $session = Session::retrieve($request->session_id);

        if ($session->payment_status === 'paid') {
            $order = Order::find($session->client_reference_id);

            if ($order) {
                return redirect()->route('order.status', $order->order_number);
            }
        }

        return redirect()->route('shop.index');
    }

    public function cancel(Request $request)
    {
        $orderNumber = $request->query('order');

        if ($orderNumber) {
            return redirect()->route('order.status', $orderNumber)
                ->with('error', 'Payment was cancelled');
        }

        return redirect()->route('checkout');
    }

    public function webhook(Request $request)
    {
        $endpoint_secret = config('services.stripe.webhook_secret');

        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');

        try {
            $event = Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;

            $order = Order::find($session->client_reference_id);

            if ($order) {
                $order->update(['status' => 'paid']);

                $payment = Payment::where('order_id', $order->id)
                    ->where('provider', 'stripe')
                    ->first();

                if ($payment) {
                    $payment->markAsSucceeded([
                        'session_id' => $session->id,
                        'payment_intent' => $session->payment_intent,
                    ]);
                }
            }
        }

        return response()->json(['status' => 'success']);
    }
}
