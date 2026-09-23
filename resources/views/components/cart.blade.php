<div x-data="{ init() { this.$store.cart.init() } }" x-init="init()">
    <!-- Cart Overlay -->
    <div
        x-show="$store.cart.isOpen"
        x-transition:enter="transition-opacity ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 bg-black/30 backdrop-blur-sm"
        @click="$store.cart.toggleCart()"
        x-cloak
    ></div>

    <!-- Cart Sidebar -->
    <div
        x-show="$store.cart.isOpen"
        x-transition:enter="transform transition ease-out duration-300"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transform transition ease-in duration-200"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="fixed right-0 top-0 bottom-0 md:top-4 md:right-4 md:bottom-4 rounded-xl z-50 w-full max-w-sm bg-white shadow-2xl border border-zinc-100"
        x-cloak
    >
        <div class="flex h-full flex-col">
            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-zinc-100">
                <h2 class="text-zinc-900 text-lg font-medium tracking-wide">{{ __('Shopping Cart') }}</h2>
                <button @click="$store.cart.toggleCart()" class="text-zinc-400 hover:text-zinc-600 transition-colors duration-200">
                    <x-tabler-x class="w-5 h-5" stroke-width="1.25" />
                </button>
            </div>

            <!-- Cart Items -->
            <div class="flex-1 overflow-y-auto">
                <template x-if="$store.cart.items.length === 0">
                    <div class="flex flex-col items-center justify-center h-full text-center px-6">
                        <div class="w-16 h-16 rounded-full bg-zinc-50 flex items-center justify-center mb-4">
{{--                            <x-tabler-shopping-bag class="w-7 h-7 text-zinc-300" stroke-width="1" />--}}
                            <svg class="w-7 h-7 text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"></path>
                            </svg>
                        </div>
                        <h3 class="text-zinc-900 font-light mb-2 text-sm">{{ __('Cart is empty') }}</h3>
                        <p class="text-zinc-500 text-xs font-light">{{ __("Let's add something") }}</p>
                    </div>
                </template>

                <div class="px-6 py-4">
                    <template x-for="item in $store.cart.items" :key="item.id">
                        <div class="flex items-start gap-4 py-4 border-b border-zinc-50 last:border-b-0">
                            <!-- Product Image -->
                            <div class="flex-shrink-0 w-18 h-18 bg-zinc-50 rounded-sm overflow-hidden">
                                <a :href="'{{ app()->getLocale() !== 'en' ? '/' . app()->getLocale() . '/shop/' : '/shop/' }}' + item.product.url" class="flex-shrink-0 w-18 h-18 bg-zinc-50 rounded-sm overflow-hidden hover:opacity-80 transition-opacity duration-200">
                                    <img
                                        :src="item.product.image"
                                        :alt="item.product.title"
                                        class="w-full h-full object-cover"
                                        onerror="this.style.display='none'; this.parentElement.innerHTML='<div class=&quot;w-full h-full bg-zinc-100 flex items-center justify-center&quot;><svg class=&quot;w-4 h-4 text-zinc-300&quot; fill=&quot;none&quot; stroke=&quot;currentColor&quot; viewBox=&quot;0 0 24 24&quot;><path stroke-linecap=&quot;round&quot; stroke-linejoin=&quot;round&quot; stroke-width=&quot;1.25&quot; d=&quot;M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z&quot;></path></svg></div>'"
                                    >
                                </a>
                            </div>

                            <!-- Product Details -->
                            <div class="flex-1 min-w-0">
                                <a :href="'{{ app()->getLocale() !== 'en' ? '/' . app()->getLocale() . '/shop/' : '/shop/' }}' + item.product.url" class="text-zinc-900 text-sm font-medium truncate hover:text-zinc-600 transition-colors duration-200" x-text="item.product.title"></a>
                                <!-- Color & Size -->

                                <div x-show="item.color_id || item.size_id" class="text-[10px] text-zinc-400 font-light">
                                    <template x-if="item.color_id">
                                        <span x-text="$store.cart.getColorName(item.product.colors, item.color_id)"></span>
                                    </template>
                                    <template x-if="item.color_id && item.size_id">
                                        <span> • </span>
                                    </template>
                                    <template x-if="item.size_id">
                                        <span class="font-normal" x-text="$store.cart.getSizeName(item.product.sizes, item.size_id)"></span>
                                    </template>
                                </div>

                                <!-- Price -->
                                <div class="mt-1 flex items-center gap-2">
                                    <span class="text-zinc-900 text-xs font-medium" x-text="new Intl.NumberFormat('ru-RU').format(item.product.price) + ' €'"></span>
                                    <span
                                        x-show="item.product.price_without_sale && item.product.price_without_sale > item.product.price"
                                        class="text-zinc-400 text-[10px] line-through"
                                        x-text="item.product.price_without_sale ? new Intl.NumberFormat('ru-RU').format(item.product.price_without_sale) + ' €' : ''"
                                    ></span>
                                </div>

                                <!-- Quantity Controls -->
                                <div class="mt-2 flex items-center gap-3 border border-zinc-200 rounded-full p-1 max-w-[98px]">
                                    <button
                                        @click="$store.cart.updateQuantity(item.id, item.quantity - 1)"
                                        class="w-5 h-5 flex items-center justify-center rounded-full border border-zinc-200 hover:border-zinc-300 hover:bg-zinc-50 transition-all duration-200"
                                    >
                                        <x-tabler-minus class="w-2.5 h-2.5 text-zinc-600" stroke-width="1.5" />
                                    </button>
                                    <span class="text-xs font-light text-zinc-700 w-6 text-center" x-text="item.quantity"></span>
                                    <button
                                        @click="$store.cart.updateQuantity(item.id, item.quantity + 1)"
                                        class="w-5 h-5 flex items-center justify-center rounded-full border border-zinc-200 hover:border-zinc-300 hover:bg-zinc-50 transition-all duration-200"
                                    >
                                        <x-tabler-plus class="w-2.5 h-2.5 text-zinc-600" stroke-width="1.5" />
                                    </button>
                                </div>
                            </div>

                            <!-- Remove Button -->
                            <button
                                @click="$store.cart.removeItem(item.id)"
                                class="text-zinc-300 hover:text-zinc-500 transition-colors duration-200 p-1"
                            >
                                <x-tabler-x class="w-3.5 h-3.5" stroke-width="1.25" />
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Footer -->
            <div x-show="$store.cart.items.length > 0" class="!pt-0 px-6 py-5">
                <!-- Total -->
                <div class="flex items-center justify-between mb-2">
                    <span class="text-zinc-700 text-xs font-medium uppercase tracking-wider">{{ __('Total') }}</span>
                    <span class="text-zinc-900 text-sm font-medium" x-text="new Intl.NumberFormat('fr-FR').format($store.cart.totalPrice) + ' €'"></span>
                </div>

                <!-- Checkout Button -->
                <a href="{{ localized_route('checkout') }}" class="w-full block bg-black text-white px-4 text-center py-4 text-xs font-medium uppercase tracking-wider rounded-xl hover:bg-zinc-800 transition-colors duration-200">
                    {{ __('Place order') }}
                </a>

            </div>
        </div>
    </div>
</div>
