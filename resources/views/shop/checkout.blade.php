@section('metaTitle', __('Checkout'))
@section('metaDescription', __('Checkout page'))

<x-layout>
    <section class="checkout-page bg-neutral-50 pt-28 pb-24">
        <div class="container max-w-6xl mx-auto px-4 sm:px-6">

            <!-- Breadcrumbs -->
            <div class="text-zinc-500 text-sm mb-6">
                <a href="{{ route('home') }}" class="hover:text-zinc-700 transition-colors duration-300">{{ __('Home') }}</a>
                <span class="mx-1.5 text-zinc-300">/</span>
                <a href="{{ route('shop') }}" class="hover:text-zinc-700 transition-colors duration-300">{{ __('Shop') }}</a>
                <span class="mx-1.5 text-zinc-300">/</span>
                <span>{{ __('Checkout') }}</span>
            </div>

            <!-- Header -->
            <div class="text-center mb-12">
                <h1 class="text-3xl font-medium text-zinc-900">{{ __('Checkout') }}</h1>
                <p class="text-zinc-500 mt-2">{{ __('Complete your purchase securely') }}</p>
            </div>

            <div x-data="checkoutPage()" class="grid lg:grid-cols-[1fr_400px] gap-12">

                <form @submit.prevent="submitOrder()" class="space-y-8">

                    <!-- Contact Information -->
                    <div class="bg-white rounded-2xl p-8">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-9 h-9 rounded-full bg-zinc-100 flex items-center justify-center shrink-0">
                                <x-tabler-user class="w-4 h-4 text-zinc-600" stroke-width="1.5" />
                            </div>
                            <h2 class="text-xl font-medium text-zinc-900">{{ __('Contact Information') }}</h2>
                        </div>

                        <div class="space-y-6">
                            <!-- Name & Second Name -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <input
                                        type="text"
                                        x-model="form.name"
                                        placeholder="{{ __('First Name') }}"
                                        class="w-full px-4 py-3 border border-zinc-200 rounded-xl text-sm font-medium placeholder-zinc-400 focus:outline-none focus:border-zinc-400 transition-colors duration-200"
                                        required
                                    >
                                    <div x-show="errors.name" class="text-red-500 text-xs mt-1.5" x-text="errors.name"></div>
                                </div>
                                <div>
                                    <input
                                        type="text"
                                        x-model="form.secondname"
                                        placeholder="{{ __('Second Name') }}"
                                        class="w-full px-4 py-3 border border-zinc-200 rounded-xl text-sm font-medium placeholder-zinc-400 focus:outline-none focus:border-zinc-400 transition-colors duration-200"
                                        required
                                    >
                                    <div x-show="errors.secondname" class="text-red-500 text-xs mt-1.5" x-text="errors.secondname"></div>
                                </div>
                            </div>

                            <!-- Email & Phone -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="relative">
                                    <x-tabler-mail class="w-4 h-4 text-zinc-400 absolute left-4 top-1/2 -translate-y-1/2" stroke-width="1.5" />
                                    <input
                                        type="email"
                                        x-model="form.email"
                                        placeholder="{{ __('E-mail') }}"
                                        class="w-full pl-10 pr-4 py-3 border border-zinc-200 rounded-xl text-sm font-medium placeholder-zinc-400 focus:outline-none focus:border-zinc-400 transition-colors duration-200"
                                    >
                                    <div x-show="errors.email" class="text-red-500 text-xs mt-1.5" x-text="errors.email"></div>
                                </div>
                                <div class="relative">
                                    <x-tabler-phone class="w-4 h-4 text-zinc-400 absolute left-4 top-1/2 -translate-y-1/2" stroke-width="1.5" />
                                    <input
                                        type="tel"
                                        x-model="form.phone"
                                        placeholder="{{ __('Phone Number') }}"
                                        class="w-full pl-10 pr-4 py-3 border border-zinc-200 rounded-xl text-sm font-medium placeholder-zinc-400 focus:outline-none focus:border-zinc-400 transition-colors duration-200"
                                        required
                                    >
                                    <div x-show="errors.phone" class="text-red-500 text-xs mt-1.5" x-text="errors.phone"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Address -->
                    <div class="bg-white rounded-2xl p-8">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-9 h-9 rounded-full bg-zinc-100 flex items-center justify-center shrink-0">
                                <x-tabler-map-pin class="w-4 h-4 text-zinc-600" stroke-width="1.5" />
                            </div>
                            <h2 class="text-xl font-medium text-zinc-900">{{ __('Shipping Address') }}</h2>
                        </div>

                        <div class="grid gap-4">
                            <div class="relative">
                                <x-tabler-world class="w-4 h-4 text-zinc-400 absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none" stroke-width="1.5" />
                                <select
                                    x-model="form.country"
                                    @change="updateDeliveryCost()"
                                    class="w-full pl-10 pr-4 py-3 border border-zinc-200 rounded-xl text-sm font-medium text-zinc-700 focus:outline-none focus:border-zinc-400 transition-colors duration-200 appearance-none bg-white"
                                    required
                                >
                                    <option value="" disabled selected>{{ __('Country') }}</option>
                                    @php
                                        $deliveries = \App\Models\Delivery::where('is_active', true)
                                            ->orderBy('sort_order')
                                            ->get();
                                    @endphp
                                    @foreach($deliveries as $delivery)
                                        <option value="{{ $delivery->iso_code }}">{{ $delivery->country_region }}</option>
                                    @endforeach
                                </select>
                                <x-tabler-chevron-down class="w-4 h-4 text-zinc-400 absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none" stroke-width="1.5" />
                            </div>
                            <div x-show="errors.country" class="text-red-500 text-xs -mt-2" x-text="errors.country"></div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <input
                                        type="text"
                                        x-model="form.city"
                                        placeholder="{{ __('City') }}"
                                        required
                                        class="w-full px-4 py-3 border border-zinc-200 rounded-xl text-sm font-medium placeholder-zinc-400 focus:outline-none focus:border-zinc-400 transition-colors duration-200"
                                    >
                                    <div x-show="errors.city" class="text-red-500 text-xs mt-1.5" x-text="errors.city"></div>
                                </div>
                                <div>
                                    <input
                                        type="text"
                                        x-model="form.zip"
                                        placeholder="{{ __('Postal code') }}"
                                        required
                                        class="w-full px-4 py-3 border border-zinc-200 rounded-xl text-sm font-medium placeholder-zinc-400 focus:outline-none focus:border-zinc-400 transition-colors duration-200"
                                    >
                                    <div x-show="errors.zip" class="text-red-500 text-xs mt-1.5" x-text="errors.zip"></div>
                                </div>
                            </div>

                            <div>
                                <textarea
                                    x-model="form.address"
                                    placeholder="{{ __('Full delivery address') }}"
                                    rows="4"
                                    required
                                    class="w-full px-4 py-3 border border-zinc-200 rounded-xl text-sm font-medium placeholder-zinc-400 focus:outline-none focus:border-zinc-400 transition-colors duration-200 resize-none"
                                ></textarea>
                                <div x-show="errors.address" class="text-red-500 text-xs mt-1.5" x-text="errors.address"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Privacy + Submit -->
                    <div class="bg-white rounded-2xl p-8">
                        <label class="flex items-start gap-3 text-sm text-zinc-500 mb-6">
                            <input
                                type="checkbox"
                                x-model="form.privacy"
                                required
                                class="mt-0.5 w-4 h-4 border-zinc-300 rounded text-black focus:ring-black shrink-0"
                            >
                            <span>{!! __('I agree with the') !!} <a href="/privacy-policy" class="underline hover:no-underline" target="_blank" rel="noopener">{{ __('Privacy Policy') }}</a></span>
                        </label>
                        <div x-show="errors.privacy" class="text-red-500 text-xs -mt-4 mb-4" x-text="errors.privacy"></div>

                        <button
                            type="submit"
                            :disabled="isSubmitting || $store.cart.items.length === 0"
                            class="w-full bg-black text-white rounded-xl py-4 hover:bg-zinc-800 disabled:bg-zinc-300 disabled:cursor-not-allowed transition-colors duration-200 flex items-center justify-center gap-2"
                        >
                            <span x-show="!isSubmitting" class="flex items-center gap-2">
                                <x-tabler-lock class="w-4 h-4" stroke-width="1.5" />
                                {{ __('Place Order') }}
                            </span>
                            <span x-show="isSubmitting" class="flex items-center gap-2">
                                <x-tabler-loader-2 class="w-4 h-4 animate-spin" stroke-width="1.5" />
                                {{ __('Processing...') }}
                            </span>
                        </button>

                        <div x-show="errors.general" class="text-red-500 text-sm text-center mt-4" x-text="errors.general"></div>

                        <div class="mt-6">
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
                    </div>
                </form>

                <!-- Order Summary -->
                <div class="lg:sticky lg:top-32 h-fit">
                    <div class="bg-white rounded-2xl p-8">
                        <h2 class="text-xl font-medium text-zinc-900 mb-6">{{ __('Your Order') }}</h2>

                        <template x-if="$store.cart.items.length === 0">
                            <div class="text-center py-12">
                                <x-tabler-shopping-bag class="w-16 h-16 text-zinc-300 mx-auto mb-4" stroke-width="1" />
                                <p class="text-zinc-500 text-sm">{{ __('Your cart is empty') }}</p>
                                <a href="{{ route('shop') }}" class="inline-block mt-4 text-zinc-900 text-sm underline hover:no-underline">{{ __('Return to Shop') }}</a>
                            </div>
                        </template>

                        <!-- Products Preview -->
                        <div class="space-y-4 mb-2">
                            <template x-for="item in $store.cart.items" :key="item.id">
                                <div class="flex gap-4">
                                    <div class="relative shrink-0">
                                        <img
                                            :src="item.product.image"
                                            :alt="item.product.title"
                                            class="w-16 h-20 object-cover rounded-lg bg-zinc-50"
                                        >
                                        <span
                                            class="absolute -top-2 -right-2 w-5 h-5 bg-black text-white rounded-full flex items-center font-medium justify-center text-[10px]"
                                            x-text="item.quantity"
                                        ></span>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-medium text-sm text-zinc-900" x-text="item.product.title"></p>
                                        <div x-show="item.color_id || item.size_id" class="text-xs text-zinc-500 mt-1">
                                            <template x-if="item.color_id">
                                                <span x-text="$store.cart.getColorName(item.product.colors, item.color_id)"></span>
                                            </template>
                                            <template x-if="item.color_id && item.size_id">
                                                <span> • </span>
                                            </template>
                                            <template x-if="item.size_id">
                                                <span x-text="$store.cart.getSizeName(item.product.sizes, item.size_id)"></span>
                                            </template>
                                        </div>
                                    </div>
                                    <p class="font-medium text-sm text-zinc-900 whitespace-nowrap" x-text="formatPrice(item.product.price)"></p>
                                </div>
                            </template>
                        </div>

                        <!-- Costs Breakdown -->
                        <div x-show="$store.cart.items.length > 0" class="space-y-3 text-sm pt-6 mt-6 border-t border-zinc-100">
                            <div class="flex justify-between">
                                <span class="text-zinc-500">{{ __('Subtotal') }}</span>
                                <span class="font-medium" x-text="formatPrice($store.cart.totalPrice)"></span>
                            </div>

                            <div x-show="form.country" class="flex justify-between">
                                <span class="text-zinc-500">{{ __('Delivery') }}</span>
                                <span
                                    class="font-medium"
                                    :class="{ 'text-green-600': isFreeDelivery }"
                                    x-text="deliveryCostDisplay"
                                ></span>
                            </div>
                        </div>

                        <div x-show="$store.cart.items.length > 0" class="flex justify-between text-lg font-medium mt-6 pt-6 border-t border-zinc-100">
                            <span>{{ __('Total') }}</span>
                            <span x-text="formatPrice(totalWithDelivery)"></span>
                        </div>

                        <!-- Security Notice -->
                        <div class="mt-6 text-center text-sm text-zinc-500 flex items-center justify-center gap-2">
                            <x-tabler-shield-check class="w-4 h-4 text-green-500" stroke-width="1.5" />
                            {{ __('Secure Checkout') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        function checkoutPage() {
            return {
                init() {
                    const items = this.$store.cart.items;

                    if (!items.length) {
                        return;
                    }

                    const categories = [
                        ...new Set(
                            items
                                .map(item => item.product.category)
                                .filter(Boolean)
                        )
                    ];

                    window.metaTrack?.('InitiateCheckout', {
                        content_type: 'product',
                        content_ids: items.map(item =>
                            String(item.product_id ?? item.product.id)
                        ),
                        content_name: 'Checkout',
                        content_category: categories.join(', '),
                        value: Number(this.$store.cart.totalPrice),
                        currency: 'EUR',
                        num_items: Number(this.$store.cart.totalItems),
                    });
                },

                form: {
                    name: '',
                    secondname: '',
                    phone: '',
                    email: '',
                    country: '',
                    city: '',
                    zip: '',
                    address: '',
                    message: '',
                    privacy: false
                },
                errors: {},
                isSubmitting: false,
                deliveryCost: 0,
                isFreeDelivery: false,

                get totalWithDelivery() {
                    const subtotal = this.$store.cart.totalPrice;
                    const delivery = this.isFreeDelivery ? 0 : parseFloat(this.deliveryCost);
                    return subtotal + delivery;
                },

                get deliveryCostDisplay() {
                    if (this.isFreeDelivery) {
                        return '{{ __('FREE') }}';
                    }
                    return this.formatPrice(this.deliveryCost);
                },

                formatPrice(price) {
                    return new Intl.NumberFormat('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }).format(price) + ' €';
                },

                async updateDeliveryCost() {
                    if (!this.form.country) {
                        this.deliveryCost = 0;
                        this.isFreeDelivery = false;
                        return;
                    }

                    try {
                        const response = await fetch(`/api/delivery-cost/${this.form.country}`);
                        const data = await response.json();

                        if (data.success) {
                            const subtotal = this.$store.cart.totalPrice;
                            this.isFreeDelivery = subtotal >= 50050;
                            this.deliveryCost = data.delivery_cost;
                        } else {
                            this.errors.country = data.message;
                            this.deliveryCost = 0;
                        }
                    } catch (error) {
                        console.error('Error fetching delivery cost:', error);
                        this.errors.country = '{{ __('Unable to calculate delivery cost') }}';
                    }
                },

                async submitOrder() {
                    this.errors = {};
                    this.isSubmitting = true;

                    const formData = new FormData();
                    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                    formData.append('name', this.form.name);
                    formData.append('secondname', this.form.secondname);
                    formData.append('phone', this.form.phone);
                    formData.append('email', this.form.email);
                    formData.append('country', this.form.country);
                    formData.append('city', this.form.city);
                    formData.append('zip', this.form.zip);
                    formData.append('address', this.form.address);
                    formData.append('message', this.form.message);
                    formData.append('privacy', this.form.privacy ? '1' : '0');
                    formData.append('cart_items', JSON.stringify(this.$store.cart.items));

                    try {
                        const response = await fetch('/checkout', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        const data = await response.json();

                        if (data.success === true) {
                            this.$store.cart.clearCart();
                            window.location.href = data.redirect;
                        } else {
                            console.warn('Order validation errors:', data.errors);
                            this.errors = data.errors || { general: '{{ __('An error occurred while processing your order') }}' };
                        }
                    } catch (error) {
                        console.error('Fetch error:', error);
                        this.errors.general = '{{ __('An error occurred while sending your order') }}: ' + error.message;
                    } finally {
                        this.isSubmitting = false;
                    }
                }
            }
        }
    </script>

</x-layout>
