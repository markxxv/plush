<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductCollection;
use Illuminate\Support\Facades\Http;

class Shop extends Controller
{
    public function index(Request $request)
    {
        $locale = app()->getLocale();
        $slugColumn = 'slug_' . $locale;

        $collections = \App\Models\ProductCollection::query()
            ->where('active', true)
            ->orderBy('position')
            ->get();

        $currentCollection = null;

        if ($request->filled('collection')) {
            $currentCollection = $collections
                ->firstWhere($slugColumn, $request->string('collection')->toString());

            abort_unless($currentCollection, 404);
        }

        $products = \App\Models\Product::query()
            ->where('active', true)
            ->when(
                $currentCollection,
                fn ($query) => $query->whereHas(
                    'collections',
                    fn ($query) => $query->whereKey($currentCollection->id)
                )
            )
            ->with([
                'sizes',
                'collections',
                'media' => fn ($query) => $query
                    ->where('collection_name', 'gallery')
                    ->orderBy('order_column'),
            ])
            ->latest('created_at')
            ->paginate(12)
            ->withQueryString();

        return view('shop.index', [
            'products' => $products,
            'collections' => $collections,
            'currentCollection' => $currentCollection,
        ]);
    }

   public function product($url)
    {
        $locale = app()->getLocale();
        $slugColumn = 'slug_' . $locale;

        $product = Product::query()
            ->where($slugColumn, $url)
            ->where('active', true)
            ->with([
                'sizes',
                'collections',
                'reviews' => fn ($query) => $query->where('active', true)->latest('created_at'),
                'media' => fn ($query) => $query
                    ->where('collection_name', 'gallery')
                    ->orderBy('order_column'),
            ])
            ->firstOrFail();

        $collectionIds = $product->collections->pluck('id');

       $media = [
            'media' => fn ($query) => $query
                ->where('collection_name', 'gallery')
                ->orderBy('order_column')
                ->limit(2),
        ];

        $similar = collect();

        if ($collectionIds->isNotEmpty()) {
            $similar = Product::query()
                ->where('active', true)
                ->whereKeyNot($product->id)
                ->whereHas('collections', fn ($query) =>
                    $query->whereIn('product_collections.id', $collectionIds)
                )
                ->with($media)
                ->latest('id')
                ->limit(4)
                ->get();
        }

        if ($similar->count() < 4) {
            $excludeIds = $similar->pluck('id')->push($product->id);

            $latest = Product::query()
                ->where('active', true)
                ->whereNotIn('id', $excludeIds)
                ->when($collectionIds->isNotEmpty(), fn ($query) =>
                    $query->whereDoesntHave('collections', fn ($query) =>
                        $query->whereIn('product_collections.id', $collectionIds)
                    )
                )
                ->with($media)
                ->latest('id')
                ->limit(4 - $similar->count())
                ->get();

            $similar = $similar->merge($latest);
        }

        return view('shop.product', [
            'product' => $product,
            'similar' => $similar,
        ]);
    }

    public function collections()
    {
        $collections = ProductCollection::query()
            ->where('active', true)
            ->orderBy('position')
            ->with([
                'products' => fn ($query) => $query
                    ->where('products.active', true)
                    ->select([
                        'products.id',
                        'products.title_en',
                        'products.title_fr',
                        'products.slug_en',
                        'products.slug_fr',
                        'products.price',
                    ])
                    ->with([
                        'media' => fn ($query) => $query
                            ->where('collection_name', 'gallery')
                            ->orderBy('order_column')
                            ->limit(2),
                    ])
                    ->latest('products.id')
                    ->limit(4),
            ])
            ->get();

        return view('shop.collections', [
            'collections' => $collections,
        ]);
    }

    public function collection($url)
    {
        $locale = app()->getLocale();

        $collection = ProductCollection::query()
            ->where('active', true)
            ->where('slug_' . $locale, $url)
            ->firstOrFail();

        $products = $collection
            ->products()
            ->where('products.active', true)
            ->with([
                'media' => fn ($query) => $query
                    ->where('collection_name', 'gallery')
                    ->orderBy('order_column')
                    ->limit(2),
            ])
            ->latest('products.id')
            ->paginate(24);

        return view('shop.index', [
            'products' => $products,
            'collection' => $collection,
            'pageTitle' => $collection->{'name_' . $locale} ?: $collection->name_en,
        ]);
    }





    public function checkout()
    {
        return view('shop.checkout');
    }

    public function processOrder(Request $request)
    {
        try {
            $request->validate([
                'name'       => 'required|string|max:255',
                'secondname' => 'required|string|max:255',
                'phone'      => 'required|string|max:20',
                'email'      => 'nullable|email|max:255',
                'country'    => 'nullable|string|max:255',
                'city'       => 'nullable|string|max:255',
                'zip'        => 'nullable|string|max:20',
                'address'    => 'nullable|string|max:1000',
            ]);

            $cartItems = json_decode($request->cart_items, true);
            $subtotal = (int) collect($cartItems)->sum(fn($item) => $item['product']['price'] * $item['quantity']);

            // Get delivery cost based on country
            $delivery = \App\Models\Delivery::where('iso_code', $request->country)
                ->where('is_active', true)
                ->first();

            if (!$delivery) {
                return response()->json([
                    'success' => false,
                    'errors' => ['country' => 'Delivery to this country is not available']
                ], 422);
            }

            // Calculate delivery cost (free if subtotal >= 300 EUR)
            //$deliveryCost = $subtotal >= 300 ? 0 : (float) $delivery->price;
            $deliveryCost = (float) $delivery->price;
            $totalAmount = $subtotal + $deliveryCost;

            $order = Order::create([
                'name' => $request->name,
                'secondname' => $request->secondname,
                'phone' => $request->phone,
                'email' => $request->email,
                'city' => $request->city,
                'message' => $request->message,
                'country' => $request->country,
                'zip' => $request->zip,
                'address' => $request->address,
                'items' => $cartItems,
                'subtotal' => $subtotal,
                'delivery_cost' => $deliveryCost,
                'total_amount' => $totalAmount,
                'status' => 'pending'
            ]);

            $this->sendTelegramNotification($order);

            return response()->json([
                'success' => true,
                'redirect' => '/order/' . $order->order_number
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Order creation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'errors' => ['general' => 'Error...']
            ], 500);
        }
    }

    public function preorder(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer'],
            'locale' => ['required', 'in:en,fr'],
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40', 'required_without:email'],
            'email' => ['nullable', 'email', 'max:255', 'required_without:phone'],
            'measurements' => ['nullable', 'string', 'max:500'],
            'comment' => ['nullable', 'string', 'max:1500'],
        ]);

        $product = Product::query()
            ->whereKey($data['product_id'])
            ->where('active', true)
            ->where('preorder', true)
            ->first();

        if (! $product) {
            return response()->json([
                'success' => false,
                'message' => 'Product is not available for pre-order.',
            ], 422);
        }

        $botToken = env('TELEGRAM_BOT_TOKEN');
        $chatId = env('TELEGRAM_CHAT_ID');

        if (! $botToken || ! $chatId) {
            \Log::warning('Pre-order Telegram notification skipped: credentials are missing.');

            return response()->json([
                'success' => false,
                'message' => 'Unable to send request.',
            ], 503);
        }

        $locale = $data['locale'];
        $title = $product->{'title_' . $locale} ?: $product->title_en;
        $slug = $product->{'slug_' . $locale} ?: $product->slug_en;
        $productUrl = localized_route(
            'shop.item',
            ['url' => $slug],
            $locale
        );

        $lines = [
            '🧵 NEW PRE-ORDER REQUEST',
            '',
            "📦 Product: {$title}",
            "🔗 {$productUrl}",
            '',
            "👤 Customer: {$data['full_name']}",
        ];

        if (! empty($data['phone'])) {
            $lines[] = "📱 Phone: {$data['phone']}";
        }

        if (! empty($data['email'])) {
            $lines[] = "📧 Email: {$data['email']}";
        }

        if (! empty($data['measurements'])) {
            $lines[] = "📏 Measurements: {$data['measurements']}";
        }

        if (! empty($data['comment'])) {
            $lines[] = "💬 Comment: {$data['comment']}";
        }

        $lines[] = '';
        $lines[] = '🌐 Language: ' . strtoupper($locale);
        $lines[] = '📅 Date: ' . now()->format('d.m.Y H:i');

        try {
            $response = Http::post(
                "https://api.telegram.org/bot{$botToken}/sendMessage",
                [
                    'chat_id' => $chatId,
                    'text' => implode("\n", $lines),
                ]
            );

            if (! $response->successful()) {
                \Log::error('Pre-order Telegram notification failed.', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Unable to send request.',
                ], 502);
            }
        } catch (\Exception $e) {
            \Log::error('Pre-order Telegram notification failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Unable to send request.',
            ], 500);
        }

        return response()->json([
            'success' => true,
        ]);
    }

    public function orderStatus($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();
        return view('shop.order-status', compact('order'));
    }

    private function sendTelegramNotification($order)
    {
        $botToken = env('TELEGRAM_BOT_TOKEN');
        $chatId = env('TELEGRAM_CHAT_ID');

        if (!$botToken || !$chatId) {
            return;
        }

        $itemsList = collect($order->items)->map(function($item) {
            $colorSize = '';
            if (isset($item['color_id']) || isset($item['size_id'])) {
                $parts = [];
                if (isset($item['color_id'])) {
                    $color = collect($item['product']['colors'])->firstWhere('id', $item['color_id']);
                    $parts[] = $color['title'] ?? '';
                }
                if (isset($item['size_id'])) {
                    $size = collect($item['product']['sizes'])->firstWhere('id', $item['size_id']);
                    $parts[] = $size['title'] ?? '';
                }
                $colorSize = ' (' . implode(' • ', array_filter($parts)) . ')';
            }

            return "• {$item['product']['title']}{$colorSize} - {$item['quantity']} pcs. - " . number_format($item['product']['price'], 0, '.', ' ') . " €";
        })->implode("\n");

        // Add delivery info
        $deliveryInfo = "";
        if ($order->delivery_cost > 0) {
            $deliveryInfo = "🚚 Delivery: " . number_format($order->delivery_cost, 2, '.', ' ') . " €\n";
        } else {
            $deliveryInfo = "🚚 Delivery: Free (order > 300€)\n";
        }

        $message = "🛍 NEW ORDER #{$order->order_number}\n\n" .
            "👤 Customer: {$order->name} {$order->secondname}\n" .
            "📱 Phone: {$order->phone}\n" .
            ($order->email ? "📧 Email: {$order->email}\n" : "") .
            ($order->city ? "🏙 City: {$order->city}\n" : "") .
            "\n📦 Items:\n{$itemsList}\n\n" .
            "💵 Subtotal: " . number_format($order->subtotal, 2, '.', ' ') . " €\n" .
            $deliveryInfo .
            "💰 Total: " . number_format($order->total_amount, 0, '.', ' ') . " €\n" .
            ($order->message ? "\n💬 Message: {$order->message}\n" : "") .
            "\n📅 Date: " . $order->created_at->format('d.m.Y H:i');

        try {
            $response = Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'HTML'
            ]);
        } catch (\Exception $e) {
            \Log::error('Telegram notification failed: ' . $e->getMessage());
        }
    }

    public function getDeliveryCost($countryCode)
    {
        $delivery = \App\Models\Delivery::where('iso_code', $countryCode)
            ->where('is_active', true)
            ->first();

        if (!$delivery) {
            return response()->json([
                'success' => false,
                'message' => 'Delivery not available for this country'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'delivery_cost' => $delivery->price,
            'country' => $delivery->country_region,
            'iso_code' => $delivery->iso_code
        ]);
    }


    public function wishlist()
    {
        return view('shop.wishlist');
    }
}
