@section('metaTitle', __('Wishlist'))
@section('metaDescription', __('Wishlist'))

<x-layout>
    <section class="wishlist-page bg-white py-16">
        <div class="container max-w-screen-xl mx-auto px-4 sm:px-6">
            <!-- Breadcrumbs -->
            <div class="text-zinc-400 text-xs mb-8">
                <a href="{{ route('home') }}" class="hover:text-zinc-700 transition-colors duration-300">{{ __('Home') }}</a>
                <span class="mx-1.5 text-zinc-200">/</span>
                <span>{{ __('Wishlist') }}</span>
            </div>

            <!-- Page Title -->
            <h1 class="text-2xl sm:text-3xl font-medium tracking-wide text-zinc-900 mb-12">{{ __('Wishlist') }}</h1>

            <div x-data="{ init() { this.$store.wishlist.init() } }" x-init="init()">
                <!-- Empty State -->
                <template x-if="$store.wishlist.items.length === 0">
                    <div class="text-center py-24">
                        <div class="w-24 h-24 bg-zinc-50 rounded-full flex items-center justify-center mx-auto mb-8">
                            <x-tabler-heart class="w-12 h-12 text-zinc-300" stroke-width="1" />
                        </div>
                        <h2 class="text-zinc-900 text-xl font-medium mb-4">{{ __('Wishlist is empty') }}</h2>
                        <p class="text-zinc-500 text-sm font-medium mb-8 max-w-md mx-auto">{{ __('Add items to your favourites so you don\'t lose them and can find them quickly later') }}</p>
                        <a href="{{ route('shop') }}" class="inline-block bg-black text-white px-8 py-3 text-sm font-medium uppercase tracking-wider rounded-sm hover:bg-zinc-800 transition-colors duration-200">
                            {{ __('Start shopping') }}
                        </a>
                    </div>
                </template>

                <!-- Wishlist Items -->
                <div x-show="$store.wishlist.items.length > 0" class="space-y-8">
                    <!-- Items Count -->
                    <div class="flex items-center justify-between border-b border-zinc-200 pb-4">
                        <p class="text-zinc-600 text-sm font-medium">
                            <span x-text="$store.wishlist.totalItems"></span>
                            <span x-text="$store.wishlist.totalItems === 1 ? '{{ __('product') }}' : '{{ __('products') }}'"></span>
                        </p>
                        <button
                            @click="confirm('{{ __('Clear wishlist?') }}') && ($store.wishlist.items = [], $store.wishlist.save())"
                            class="text-zinc-500 text-xs font-medium hover:text-red-500 transition-colors duration-200"
                        >
                            {{ __('Clear all') }}
                        </button>
                    </div>

                    <!-- Items Grid -->
                    <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-2 md:gap-6">
                        <template x-for="item in $store.wishlist.items" :key="item.id">
                            <div
                                class="wishlist-item group relative bg-white"
                                x-data="{
                x: 50, y: 50, hover: false,
                move(e) {
                    const rect = this.$refs.imgBox.getBoundingClientRect();
                    this.x = ((e.clientX - rect.left) / rect.width) * 100;
                    this.y = ((e.clientY - rect.top) / rect.height) * 100;
                }
            }"
                                @mousemove="move($event)"
                                @mouseenter="hover = true"
                                @mouseleave="hover = false"
                            >
                                <!-- Product Image -->
                                <div x-ref="imgBox" class="relative aspect-[3/4] overflow-hidden">
                                    <a :href="'{{ app()->getLocale() !== 'en' ? '/' . app()->getLocale() . '/fashion/' : '/fashion/' }}' + item.product.url" class="block w-full h-full">
                                        <img
                                            :src="item.product.image"
                                            :alt="item.product.title"
                                            class="w-full h-full object-cover object-center"
                                        >
                                    </a>

                                    <!-- Ink-drop vignette that spreads from the cursor -->
                                    <div
                                        class="pointer-events-none absolute inset-0 bg-black/25 transition-[clip-path] duration-[900ms] ease-out"
                                        :style="`clip-path: circle(${hover ? 150 : 0}% at ${x}% ${y}%)`"
                                    ></div>

                                    <!-- Floating "Remove" tag tracking the cursor -->
                                    <button
                                        @click="$store.wishlist.removeItem(item.id)"
                                        class="pointer-events-none absolute z-20 hidden md:flex items-center gap-1.5 px-3 py-1.5 bg-white text-black text-[10px] uppercase tracking-[0.15em] transition-opacity duration-300 whitespace-nowrap"
                                        :class="hover ? 'opacity-100 pointer-events-auto' : 'opacity-0'"
                                        :style="`left:${x}%; top:${y}%; transform: translate(-50%, -160%)`"
                                    >
                                        {{ __('remove') }}
                                        <x-tabler-x class="w-3 h-3" stroke-width="1.5" />
                                    </button>

                                    <!-- Mobile remove button -->
                                    <button
                                        @click="$store.wishlist.removeItem(item.id)"
                                        class="md:hidden absolute top-3 right-3 z-20 w-8 h-8 bg-white/90 backdrop-blur flex items-center justify-center"
                                    >
                                        <x-tabler-x class="w-4 h-4 text-black" stroke-width="1.5" />
                                    </button>
                                </div>

                                <!-- Product Details -->
                                <div class="pt-4">
                                    <a :href="'{{ app()->getLocale() !== 'en' ? '/' . app()->getLocale() . '/fashion/' : '/fashion/' }}' + item.product.url" class="block">
                                        <h3 class="product_title mb-3 line-clamp-2" x-text="item.product.title"></h3>
                                    </a>

                                    <!-- Actions -->
                                    <div class="flex items-center gap-2">

                                        <a :href="'{{ app()->getLocale() !== 'en' ? '/' . app()->getLocale() . '/fashion/' : '/fashion/' }}' + item.product.url"
                                        class="flex items-center justify-center gap-3 shrink-0 w-full rounded-lg p-3 border border-zinc-200 text-zinc-700 flex items-center justify-center hover:border-black hover:bg-black hover:text-white transition-colors duration-300"
                                        >
                                            <span>{{ __('View product') }}</span>
                                            <x-tabler-eye class="w-4 h-4" stroke-width="1.5" />
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                    <!-- Actions Footer -->
                    <div x-show="$store.wishlist.items.length > 0" class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-8 border-t border-zinc-200">
                        <p class="text-zinc-500 text-sm font-medium">
                            {{ __('Add items to your cart or continue shopping') }}
                        </p>
                        <div class="flex gap-3">
                            <a href="{{ app()->getLocale() !== 'en' ? '/' . app()->getLocale() . '/shop' : '/shop' }}" class="border border-zinc-200 text-zinc-700 px-6 py-3 text-sm font-medium rounded-xl hover:border-zinc-300 hover:bg-zinc-50 transition-colors duration-200">
                                {{ __('Continue shopping') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layout>
