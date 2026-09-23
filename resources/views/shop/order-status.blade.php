@section('metaTitle', __('Order') . ' #' . $order->order_number)
@section('metaDescription', __('Status of the order: ') . ' #' . $order->order_number)

<x-layout>
    <section class="order-status-page bg-white py-28">
        <div class="container max-w-screen-xl mx-auto px-4 sm:px-6">
            <div class="max-w-2xl mx-auto text-center">
                <!-- Success Icon -->
                <!-- Order Steps -->
                @php
                    $paymentCompleted = in_array($order->status, ['paid', 'processing', 'shipped', 'delivered']);
                @endphp

                <div class="max-w-md mx-auto mb-10">
                    <div class="flex items-start">
                        <!-- Step 1 -->
                        <div class="flex flex-1 flex-col items-center text-center">
                            <div class="relative z-10 flex w-12 h-12 items-center justify-center rounded-full bg-green-100">
                                <x-tabler-check
                                    class="w-6 h-6 text-green-600"
                                    stroke-width="2"
                                />
                            </div>

                            <div class="mt-3">
                                <p class="text-sm font-medium text-zinc-900">
                                    {{ __('Order Placed') }}
                                </p>
                            </div>
                        </div>

                        <!-- Connector -->
                        <div class="relative -mx-8 mt-6 h-px flex-1 bg-zinc-200">
                            <div
                                class="absolute inset-y-0 left-0 bg-green-500 transition-all duration-300
                                {{ $paymentCompleted ? 'w-full' : 'w-0' }}"
                            ></div>
                        </div>

                        <!-- Step 2 -->
                        <div class="flex flex-1 flex-col items-center text-center">
                            <div
                                class="relative z-10 flex w-12 h-12 items-center justify-center rounded-full
                                {{ $paymentCompleted ? 'bg-green-100' : 'bg-zinc-100' }}"
                            >
                                @if($paymentCompleted)
                                    <x-tabler-check
                                        class="w-6 h-6 text-green-600"
                                        stroke-width="2"
                                    />
                                @else
                                    <span class="w-2.5 h-2.5 rounded-full bg-zinc-400"></span>
                                @endif
                            </div>

                            <div class="mt-3">
                                <p
                                    class="text-sm font-medium
                                     {{ $paymentCompleted ? 'text-zinc-900' : 'text-zinc-500' }}"
                                >
                                    {{ $paymentCompleted ? __('Payment Received') : __('Awaiting Payment') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Success Message -->
                <h1 class="text-2xl sm:text-3xl font-medium tracking-wide text-zinc-900 mb-4">{{ __('Order Placed') }}</h1>
                <p class="text-zinc-600 text-sm font-medium mb-8">{{ __('Thank you for your purchase! We will contact you shortly to confirm your order.') }}</p>

                <!-- Order Details -->
                <div class="bg-zinc-50 rounded-sm p-8 mb-8 text-left">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-zinc-900 text-sm font-medium uppercase tracking-wider mb-3">{{ __('Order Number') }}</h3>
                            <p class="text-zinc-900 text-lg font-medium">#{{ $order->order_number }}</p>
                        </div>
                        <div>
                            <h3 class="text-zinc-900 text-sm font-medium uppercase tracking-wider mb-3">{{ __('Status') }}</h3>
                            <span class="inline-block px-3 py-1 text-xs font-medium uppercase tracking-wider rounded-full
                            @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                            @elseif($order->status === 'confirmed') bg-blue-100 text-blue-800
                            @elseif($order->status === 'processing') bg-purple-100 text-purple-800
                            @elseif($order->status === 'shipped') bg-orange-100 text-orange-800
                            @elseif($order->status === 'delivered') bg-green-100 text-green-800
                            @elseif($order->status === 'paid') bg-green-100 text-green-800
                            @else bg-red-100 text-gray-800
                            @endif">
                            {{ $order->status_label }}
                        </span>
                        </div>
                        <div>
                            <h3 class="text-zinc-900 text-sm font-medium uppercase tracking-wider mb-3">{{ __('Order Date') }}</h3>
                            <p class="text-zinc-700 text-sm font-normal">{{ $order->created_at->format('d.m.Y H:i') }}</p>
                        </div>
                        <div>
                            <div>
                                <h3 class="text-zinc-900 text-sm font-medium uppercase tracking-wider mb-3">{{ __('Total Amount') }}</h3>
                                <p class="text-zinc-900 text-lg font-medium">{{ number_format($order->total_amount, 2, '.', ' ') }} €</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Action -->
                @if($order->status === 'pending')
                    <div class="">
                        <form action="{{ route('payment.create-session') }}" method="POST">
                            @csrf
                            <input type="hidden" name="order_id" value="{{ $order->id }}">
                            <input type="hidden" name="customer_name" value="{{ $order->name }} {{ $order->secondname }}">
                            <input type="hidden" name="customer_email" value="{{ $order->email }}">
                            <input type="hidden" name="customer_country" value="{{ $order->country }}">
                            <button type="submit" class="w-full bg-black text-white px-8 py-4 text-sm font-medium uppercase tracking-wider rounded-xl hover:bg-zinc-800 transition-colors duration-200">
                                {{ __('Pay Now') }} - {{ number_format($order->total_amount, 2, '.', ' ') }} €
                            </button>
                        </form>
                    </div>
                    <div class="py-6">
                        <div class="flex items-center justify-center gap-2">
                                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-emerald-50 text-emerald-700">
                                    <svg
                                        class="h-3.5 w-3.5"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        aria-hidden="true"
                                    >
                                        <rect x="5" y="10" width="14" height="10" rx="3"/>
                                        <path d="M8 10V7a4 4 0 0 1 8 0v3"/>
                                    </svg>
                                </span>

                            <div>
                                <div class="text-[12px] font-medium tracking-[-0.01em] text-zinc-900">
                                    Secure payments
                                </div>
                                <div class="mt-0.5 text-[10px] text-zinc-400">
                                    Your payment information is protected
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 flex flex-wrap items-center justify-center gap-x-3 gap-y-3">
                            <img src="/img/visa.svg" alt="Visa" class="w-8">
                            <img src="/img/mastercard.svg" alt="Mastercard" class="w-8">
                            <img src="/img/amex.svg" alt="American Express" class="w-8">
                            <img src="/img/paypal.svg" alt="PayPal" class="w-8">
                            <img src="/img/apple-pay.svg" alt="Apple Pay" class="w-8">
                        </div>
                    </div>
                @endif

                <!-- Contact Info -->
                <div class="bg-zinc-50 rounded-sm p-8 mb-8 text-left">
                    <h3 class="text-zinc-900 text-sm font-medium uppercase tracking-wider mb-4">{{ __('Contact Information') }}</h3>
                    <div class="space-y-2 text-sm text-zinc-700 font-normal">
                        <p><span class="font-medium">{{ __('Name') }}:</span> {{ $order->name }} {{ $order->secondname }}</p>
                        <p><span class="font-medium">{{ __('Phone') }}:</span> {{ $order->phone }}</p>
                        @if($order->email)
                            <p><span class="font-medium">{{ __('Email') }}:</span> {{ $order->email }}</p>
                        @endif
                        @if($order->city)
                            <p><span class="font-medium">{{ __('City') }}:</span> {{ $order->city }}</p>
                        @endif
                    </div>
                </div>

                <!-- Order Items -->
                <!-- Order Items -->
                <div class="bg-zinc-50 rounded-sm p-8 mb-8 text-left">
                    <h3 class="text-zinc-900 text-sm font-medium uppercase tracking-wider mb-4">{{ __('Ordered Items') }}</h3>
                    <div class="space-y-4">
                        @foreach($order->items as $item)
                            <div class="flex items-start gap-4 py-4 border-b border-zinc-200">
                                <div class="flex-shrink-0 w-16 h-16 bg-white rounded-sm overflow-hidden">
                                    <img src="{{ $item['product']['image'] }}" alt="{{ $item['product']['title'] }}" class="w-full h-full object-cover">
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-zinc-900 text-sm font-medium">{{ $item['product']['title'] }}</h4>
                                    @if(isset($item['color_id']) || isset($item['size_id']))
                                        <div class="mt-1 text-xs text-zinc-500">
                                            @if(isset($item['color_id']))
                                                {{ collect($item['product']['colors'])->firstWhere('id', $item['color_id'])['title'] ?? '' }}
                                            @endif
                                            @if(isset($item['color_id']) && isset($item['size_id'])) • @endif
                                            @if(isset($item['size_id']))
                                                {{ collect($item['product']['sizes'])->firstWhere('id', $item['size_id'])['title'] ?? '' }}
                                            @endif
                                        </div>
                                    @endif
                                    <div class="mt-2 flex items-center justify-between">
                                        <span class="text-zinc-900 text-sm">{{ number_format($item['product']['price'], 2, '.', ' ') }} €</span>
                                        <span class="text-zinc-600 text-sm">× {{ $item['quantity'] }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <!-- Subtotal, Delivery, Total -->
                        <div class="pt-4 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-zinc-600">{{ __('Subtotal') }}:</span>
                                <span class="text-zinc-900">{{ number_format($order->subtotal, 2, '.', ' ') }} €</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-zinc-600">{{ __('Delivery') }}:</span>
                                <span class="text-zinc-900">{{ number_format($order->delivery_cost, 2, '.', ' ') }} €</span>
                            </div>
                            <div class="flex justify-between text-base font-medium pt-2 border-t border-zinc-200">
                                <span class="text-zinc-900">{{ __('Total') }}:</span>
                                <span class="text-zinc-900">{{ number_format($order->total_amount, 2, '.', ' ') }} €</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="space-y-4">
                    <a href="{{ route('shop') }}" class="inline-block bg-black text-white px-8 py-4 font-medium tracking-wider rounded-xl hover:bg-zinc-800 transition-colors duration-200">
                        {{ __('Continue Shopping') }}
                    </a>

                    <div class="text-center">
                        <p class="text-zinc-500 text-sm font-normal">{{ __('If you have any questions about your order, please contact us at') }} <a href="mailto:plush.maison.official@gmail.com" class="text-zinc-900 hover:underline">plush.maison.official@gmail.com</a></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

</x-layout>
