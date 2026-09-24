@php
    $locale = app()->getLocale();

    $title = $product->{'title_' . $locale} ?: $product->title_en;
    $description = $product->{'description_' . $locale} ?: $product->description_en;

    $metaTitle = $product->{'meta_title_' . $locale} ?: $title ?: 'Maison Plush Paris';
    $metaDescription = $product->{'meta_description_' . $locale} ?: '';

    $collection = $product->collections->first();
    $collectionName = $collection?->{'name_' . $locale} ?: $collection?->name_en;

    $collectionDescription = $collection?->{'description_' . $locale}
        ?? $collection?->description_en
        ?? null;
@endphp

@section('metaTitle', $metaTitle)
@section('metaDescription', $metaDescription)

<x-layout>
    <main class="">
        <div class="container">
            <div data-product-hero class="grid md:grid-cols-2 lg:grid-cols-[1fr_600px] lg:gap-12 xl:gap-24 items-center">

                {{-- Product Gallery --}}
                <section class="relative">
                    @php
                        $galleryMedia = $product->getMedia('gallery');
                        $imagesData = $galleryMedia->map(function($media) use ($product) {
                            return [
                                'large' => $media->getUrl('large'),
                                'largea' => $media->getUrl('largea'),
                                'thumb' => $media->getUrl('thumb'),
                                'alt' => $media->getCustomProperty('alt') ?: $product->title
                            ];
                        });
                    @endphp
                    <x-gallery :gallery-media="$galleryMedia" :images-data="$imagesData" />

                </section>

                {{-- Product Info --}}
                <section
                    class="order-1 md:order-2 md:py-28"
                    x-data="productPage(@js([
                        'id' => $product->id,
                        'title' => $title,
                        'price' => (float) $product->price,
                        'image' => $product->getFirstMediaUrl('gallery', 'thumb'),
                        'url' => url()->current(),
                        'category' => $collectionName ?? '',
                        'sizes' => $product->sizes
                            ->map(fn ($s) => [
                                'id' => $s->id,
                                'title' => $s->value,
                            ])
                            ->values(),
                    ]))"
                >
                    <div class="md:sticky top-32">

                        <nav class="hidden md:flex gap-2 text-sm text-neutral-500 mb-8 font-normal">
                            <a href="/">{{ __('Home') }}</a>
                            <span class="text-zinc-300">/</span>
                            <a href="/shop">{{ __('Shop') }}</a>
                            @if($collectionName)
                                <span class="text-zinc-300">/</span>
                                <span>{{ $collectionName }}</span>
                            @endif
                        </nav>

                        <div class="mb-8">
                            @if($collectionName)
                                <p class="text-xs uppercase font-normal tracking-[0.18em] text-neutral-400">
                                    {{ $collectionName }}
                                </p>
                            @endif

                            <h1 class="text-3xl md:text-4xl font-medium leading-tight">
                                {{ $title }}
                            </h1>

                            @if($product->price > 0)
                            <p class="text-2xl md:text-3xl font-medium mt-6">
                                {{ number_format((float) $product->price, 0) }}€
                            </p>
                            @endif

                        </div>

                        @if($product->sizes->isNotEmpty())
                            <div class="mb-8">
                                <div class="flex justify-between mb-2">
                                    <p class="font-medium">{{ __('Select Size') }}</p>
{{--                                    <button class="text-neutral-500 text-sm">Size Guide</button>--}}
                                </div>

                                <div class="grid grid-cols-4 gap-2">
                                    @foreach($product->sizes as $size)
                                        <label class="block cursor-pointer">
                                            <input
                                                type="radio"
                                                name="size"
                                                value="{{ $size->id }}"
                                                x-model.number="selectedSizeId"
                                                @change="errors.size = null"
                                                class="sr-only"
                                            >

                                            <span
                                                class="flex h-12 w-full items-center justify-center rounded-xl border text-sm font-medium transition"
                                                :class="selectedSizeId === {{ $size->id }}
                                                    ? 'border-black bg-black text-white'
                                                    : 'border-neutral-200 bg-white text-black hover:border-black'"
                                                                                    >
                                                {{ $size->value }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                                <div x-show="errors.size" class="text-red-400 mt-2 font-medium">{{ __('Select Size') }}</div>
                            </div>
                        @endif

                        <div class="space-y-3 font-normal">
                            @if($product->preorder)
                                <x-preorder :product="$product" />
                            @else
                                @if($product->availability)
                                <button type="button" @click="addToCart()" class="flex items-center justify-center gap-4  w-full bg-black text-white rounded-xl py-4 hover:bg-neutral-800 transition">
                                    <span>{{ __('Add to Cart') }}</span>
                                    <svg class="size-5 text-zinc-500" stroke-width="1.3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"></path>
                                    </svg>
                                </button>
                                @else
                                    <div class="flex items-center gap-3 lg:gap-6 bg-zinc-50 border border-zinc-200 rounded-xl px-5 py-4">
                                        <x-tabler-alert-circle class="w-5 h-5 text-zinc-400 shrink-0" stroke-width="1.5" />
                                        <p class="text-sm font-medium text-zinc-600">
                                            @if(app()->getLocale() == 'en')
                                            This item is currently out of stock. Check back soon or reach out and we'll let you know when it's available
                                            @endif
                                            @if(app()->getLocale() == 'fr')
                                                Cet article est actuellement en rupture de stock. Revenez bientôt ou contactez-nous : nous vous informerons dès qu’il sera de nouveau disponible.
                                            @endif
                                        </p>
                                    </div>
                                @endif
                            @endif

                            <button
                                type="button"
                                onclick="event.preventDefault(); Alpine.store('wishlist').toggleItem({{ json_encode([
                                    'id' => $product->id,
                                    'title' => $product->title,
                                    'price' => $product->price,
                                    'price_without_sale' => $product->price_without_sale,
                                    'image' => $product->getFirstMediaUrl('gallery', 'thumb'),
                                    'url' => $product->url
                                ]) }})"
                                x-data
                                class="flex items-center justify-center gap-4 w-full bg-white shadow-sm rounded-xl px-4 py-4 ring-1 ring-neutral-100 hover:ring-neutral-200 transition">
                                <template x-if="$store.wishlist.items.some(item => item.id === {{ $product->id }})">
                                    <span class="flex items-center justify-center gap-4">
                                        <span>{{ __('Remove from Wishlist') }}</span>
                                        <x-tabler-heart-filled class="size-5 text-rose-500" stroke-width="1.3" />
                                    </span>
                                </template>

                                <template x-if="!$store.wishlist.items.some(item => item.id === {{ $product->id }})">
                                    <span class="flex items-center justify-center gap-4">
                                        <span>{{ __('Add to Wishlist') }}</span>
                                        <x-tabler-heart class="size-5 text-zinc-500" stroke-width="1.3" />
                                    </span>
                                </template>
                            </button>
                        </div>

                        @php
                            $shareUrl = url()->current();
                            $shareTitle = $title ?? $product->title_en;
                            $shareImage = $mainImage ?? $product->getFirstMediaUrl('gallery', 'large');

                            $encodedUrl = rawurlencode($shareUrl);
                            $encodedTitle = rawurlencode($shareTitle);
                            $encodedImage = rawurlencode($shareImage);
                        @endphp

                        <div class="mt-8 flex items-center gap-4">
                            <span class="text-sm text-neutral-400">{{ __('Share') }}:</span>

                            <nav class="flex items-center gap-2">
                                <a
                                    href="mailto:?subject={{ rawurlencode('Check this ' . $shareTitle) }}&body={{ $encodedUrl }}"
                                    class="w-9 h-9 rounded-full bg-white shadow flex items-center justify-center text-neutral-500 hover:text-black hover:ring-black transition"
                                    aria-label="Share by email"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z" />
                                        <path d="M3 7l9 6l9 -6" />
                                    </svg>
                                </a>

                                <a
                                    href="https://www.facebook.com/sharer/sharer.php?u={{ $encodedUrl }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="w-9 h-9 rounded-full bg-white shadow flex items-center justify-center text-neutral-500 hover:text-black hover:ring-black transition"
                                    aria-label="Share on Facebook"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M7 10v4h3v7h4v-7h3l1 -4h-4v-2a1 1 0 0 1 1 -1h3v-4h-3a5 5 0 0 0 -5 5v2h-3" />
                                    </svg>
                                </a>

                                <a
                                    href="https://pinterest.com/pin/create/button/?url={{ $encodedUrl }}&media={{ $encodedImage }}&description={{ $encodedTitle }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="w-9 h-9 rounded-full bg-white shadow flex items-center justify-center text-neutral-500 hover:text-black hover:ring-black transition"
                                    aria-label="Share on Pinterest"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M8 20l4 -9" />
                                        <path d="M10.7 14c.437 .5 1.125 1 2.3 1a5 5 0 1 0 -5 -5c0 1.5 .5 2.5 1.5 3.5" />
                                        <path d="M12 22a10 10 0 1 0 -10 -10a10 10 0 0 0 10 10z" />
                                    </svg>
                                </a>

                                <a
                                    href="https://twitter.com/intent/tweet?url={{ $encodedUrl }}&text={{ $encodedTitle }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="w-9 h-9 rounded-full bg-white shadow flex items-center justify-center text-neutral-500 hover:text-black hover:ring-black transition"
                                    aria-label="Share on X"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 4l11.733 16h4.267l-11.733 -16z" />
                                        <path d="M4 20l6.768 -6.768" />
                                        <path d="M13.228 10.772l6.772 -6.772" />
                                    </svg>
                                </a>
                            </nav>
                        </div>

                        <div class="my-8 grid grid-cols-3 gap-4 text-xs text-neutral-500">
                            @if(app()->getLocale() == 'en')
                                <div>
                                    <p class="text-black font-medium mb-1">Delivery</p>
                                    <p>Europe-wide shipping</p>
                                </div>

                                <div>
                                    <p class="text-black font-medium mb-1">Returns</p>
                                    <p>30-day return policy</p>
                                </div>

                                <div>
                                    <p class="text-black font-medium mb-1">Support</p>
                                    <p>Secure checkout</p>
                                </div>
                            @endif

                            @if(app()->getLocale() == 'fr')
                                <div>
                                    <p class="text-black font-medium mb-1">Livraison</p>
                                    <p>Livraison dans toute l’Europe</p>
                                </div>

                                <div>
                                    <p class="text-black font-medium mb-1">Retours</p>
                                    <p>Retours sous 30 jours</p>
                                </div>

                                <div>
                                    <p class="text-black font-medium mb-1">Paiement</p>
                                    <p>Paiement sécurisé</p>
                                </div>
                            @endif
                        </div>

                    </div>

                    @if(!$product->preorder && $product->availability)
                        <div
                            x-show="quickBuyVisible"
                            x-cloak
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="translate-y-4 opacity-0"
                            x-transition:enter-end="translate-y-0 opacity-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="translate-y-0 opacity-100"
                            x-transition:leave-end="translate-y-4 opacity-0"
                            class="pointer-events-none fixed inset-x-0 bottom-0 z-50 p-2 pb-[max(0.5rem,env(safe-area-inset-bottom))] md:left-auto md:right-4 md:bottom-4 md:w-[360px] md:p-0"
                        >
                            <div class="pointer-events-auto bg-white p-2 shadow-2xl rounded-t-2xl md:rounded-2xl">
                                @if($product->sizes->count() > 1)
                                    <div
                                        x-show="quickBuyOpen"
                                        x-cloak
                                        x-transition:enter="transition ease-out duration-200"
                                        x-transition:enter-start="translate-y-2 opacity-0"
                                        x-transition:enter-end="translate-y-0 opacity-100"
                                        x-transition:leave="transition ease-in duration-150"
                                        x-transition:leave-start="translate-y-0 opacity-100"
                                        x-transition:leave-end="translate-y-2 opacity-0"
                                        class="hidden md:block pb-2"
                                    >
                                        <div class="flex items-center justify-between gap-3 px-1 pb-2">
                                            <p class="text-xs font-medium text-neutral-500">
                                                {{ __('Select Size') }}
                                            </p>

                                            <button
                                                type="button"
                                                @click="quickBuyOpen = false"
                                                class="flex size-7 items-center justify-center rounded-full bg-zinc-100 text-zinc-500 transition hover:bg-zinc-200 hover:text-black"
                                                aria-label="{{ __('Close') }}"
                                            >
                                                <x-tabler-x class="size-4" stroke-width="1.5" />
                                            </button>
                                        </div>

                                        <div class="grid grid-cols-4 gap-2">
                                            @foreach($product->sizes as $size)
                                                <label class="block cursor-pointer">
                                                    <input
                                                        type="radio"
                                                        name="quick-size-desktop"
                                                        value="{{ $size->id }}"
                                                        x-model.number="selectedSizeId"
                                                        @change="errors.size = null; confirmQuickBuy()"
                                                        class="sr-only"
                                                    >

                                                    <span
                                                        class="flex h-10 w-full items-center justify-center rounded-xl text-sm font-medium ring-1 transition"
                                                        :class="selectedSizeId === {{ $size->id }}
                                                            ? 'bg-black text-white ring-black'
                                                            : 'bg-white text-black ring-zinc-200 hover:ring-black'"
                                                    >
                                                        {{ $size->value }}
                                                    </span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <div class="flex items-center gap-2">
                                    @if($product->getFirstMediaUrl('gallery', 'thumb'))
                                        <img
                                            src="{{ $product->getFirstMediaUrl('gallery', 'thumb') }}"
                                            alt="{{ $title }}"
                                            class="hidden size-12 shrink-0 rounded-xl object-cover md:block"
                                        >
                                    @endif

                                    <div class="hidden min-w-0 flex-1 md:block">
                                        <p class="truncate text-sm font-medium text-black">
                                            {{ $title }}
                                        </p>

                                        @if($product->price > 0)
                                            <p class="mt-0.5 text-xs text-neutral-500">
                                                {{ number_format((float) $product->price, 0) }}€
                                            </p>
                                        @endif
                                    </div>

                                    <button
                                        type="button"
                                        @click="openQuickBuy()"
                                        class="flex h-11 flex-1 items-center justify-center gap-2 rounded-xl bg-black px-4 text-sm font-medium text-white transition hover:bg-neutral-800 md:h-12 md:flex-none"
                                    >
                                        <x-tabler-shopping-bag class="size-4" stroke-width="1.5" />
                                        <span>{{ __('Add to Cart') }}</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        @if($product->sizes->count() > 1)
                            <div
                                x-show="quickBuyOpen"
                                x-cloak
                                @keydown.escape.window="quickBuyOpen = false"
                                class="fixed inset-0 z-[70] md:hidden"
                            >
                                <button
                                    type="button"
                                    @click="quickBuyOpen = false"
                                    class="absolute inset-0 bg-zinc-950 opacity-20"
                                    aria-label="{{ __('Close') }}"
                                ></button>

                                <div
                                    x-show="quickBuyOpen"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="translate-y-full"
                                    x-transition:enter-end="translate-y-0"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="translate-y-0"
                                    x-transition:leave-end="translate-y-full"
                                    @click.stop
                                    class="absolute inset-x-0 bottom-0 rounded-t-2xl bg-white p-2 pb-[max(0.5rem,env(safe-area-inset-bottom))] shadow-2xl"
                                >
                                    <div class="px-2 pb-3 pt-2">
                                        <div class="mx-auto mb-4 h-1 w-10 rounded-full bg-zinc-200"></div>

                                        <div class="flex items-center justify-between gap-3">
                                            <p class="text-sm font-medium text-black">
                                                {{ __('Select Size') }}
                                            </p>

                                            <button
                                                type="button"
                                                @click="quickBuyOpen = false"
                                                class="flex size-8 items-center justify-center rounded-full bg-zinc-100 text-zinc-500"
                                                aria-label="{{ __('Close') }}"
                                            >
                                                <x-tabler-x class="size-4" stroke-width="1.5" />
                                            </button>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-4 gap-2">
                                        @foreach($product->sizes as $size)
                                            <label class="block cursor-pointer">
                                                <input
                                                    type="radio"
                                                    name="quick-size-mobile"
                                                    value="{{ $size->id }}"
                                                    x-model.number="selectedSizeId"
                                                    @change="errors.size = null; confirmQuickBuy()"
                                                    class="sr-only"
                                                >

                                                <span
                                                    class="flex h-11 w-full items-center justify-center rounded-xl text-sm font-medium ring-1 transition"
                                                    :class="selectedSizeId === {{ $size->id }}
                                                        ? 'bg-black text-white ring-black'
                                                        : 'bg-white text-black ring-zinc-200'"
                                                >
                                                    {{ $size->value }}
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                </section>
            </div>
        </div>
    </main>

    {{-- Product Tabs --}}
    <section class="bg-white py-12 lg:py-24">
        <div
            x-data="{ tab: @js(!empty($description) ? 'description' : 'collection') }"
            class="container"
        >
            <nav class="flex flex-wrap gap-6 lg:gap-0 mb-10 text-sm font-normal">
                @if($description)
                <button
                    @click="tab = 'description'"
                    :class="tab === 'description' ? 'text-black bg-neutral-100 px-5' : 'text-neutral-600 hover:text-black'"
                    class="rounded-full py-2 lg:px-5 transition"
                >
                    {{ __('Description') }}
                </button>
                @endif

                <button
                    @click="tab = 'collection'"
                    :class="tab === 'collection' ? 'text-black bg-neutral-100 px-5' : 'text-neutral-600 hover:text-black'"
                    class="rounded-full py-2 lg:px-5 transition"
                >
                    {{ __('Collection') }}
                </button>

                <button
                    @click="tab = 'shipping'"
                    :class="tab === 'shipping' ? 'text-black bg-neutral-100 px-5' : 'text-neutral-600 hover:text-black'"
                    class="rounded-full py-2 lg:px-5 transition"
                >
                    {{ __('Shipping') }}
                </button>

                @if($product->reviews()->count() > 0)
                <button
                    @click="tab = 'reviews'"
                    :class="tab === 'reviews' ? 'text-black bg-neutral-100 px-5' : 'text-neutral-600 hover:text-black'"
                    class="rounded-full py-2 lg:px-5 transition"
                >
                    {{ __('Reviews') }}
                </button>
                @endif
            </nav>

            <div class="max-w-4xl">

                @if($description)
                <article x-show="tab === 'description'" x-cloak>
                    <h2 class="text-2xl md:text-3xl font-medium mb-6">
                        {{ __('Product Description') }}
                    </h2>

                    <div class="text-neutral-600 leading-relaxed space-y-5 max-w-[600px]">
                        {!! $description !!}
                    </div>
                </article>
                @endif

                <article x-show="tab === 'collection'" x-cloak>
                    <h2 class="text-2xl md:text-3xl font-medium mb-6">
                        {{ $collectionName ?: 'Collection' }}
                    </h2>

                    <div class="text-neutral-600 leading-relaxed space-y-5 max-w-[600px]">
                        @if($collectionDescription)
                            {!! $collectionDescription !!}
                        @else
                            @if(app()->getLocale() == 'en')
                                <p>
                                    A carefully curated Maison Plush Paris selection built around clean silhouettes,
                                    soft materials and a quiet sense of confidence.
                                </p>

                                <p>
                                    Each piece is designed to work naturally with the rest of the collection — simple,
                                    wearable and refined without excess.
                                </p>
                            @endif

                            @if(app()->getLocale() == 'fr')
                                <p>
                                    Une sélection Maison Plush Paris soigneusement pensée autour de lignes épurées,
                                    de matières douces et d’une élégance discrète.
                                </p>

                                <p>
                                    Chaque pièce est conçue pour s’accorder naturellement avec le reste de la collection —
                                    simple, facile à porter et raffinée sans superflu.
                                </p>
                            @endif
                        @endif
                    </div>
                </article>

                <article x-show="tab === 'shipping'" x-cloak>
                    <h2 class="text-2xl md:text-3xl font-medium mb-8">
                        {{ __('Shipping & Returns') }}
                    </h2>

                    <div class="grid md:grid-cols-3 gap-8 text-neutral-500">
                        @if(app()->getLocale() == 'en')
                            <div>
                                <p class="text-black font-medium mb-3">Delivery</p>
                                <p>Standard European delivery is available for all orders. Express options may be available depending on destination.</p>
                            </div>

                            <div>
                                <p class="text-black font-medium mb-3">Returns</p>
                                <p>You may return unworn items within 30 days, provided they are in original condition and packaging.</p>
                            </div>

                            <div>
                                <p class="text-black font-medium mb-3">Support</p>
                                <p>For questions about sizing, delivery or care, contact Maison Plush Paris before placing your order.</p>
                            </div>
                        @endif

                        @if(app()->getLocale() == 'fr')
                            <div>
                                <p class="text-black font-medium mb-3">Livraison</p>
                                <p>La livraison standard en Europe est disponible pour toutes les commandes. Des options express peuvent être proposées selon la destination.</p>
                            </div>

                            <div>
                                <p class="text-black font-medium mb-3">Retours</p>
                                <p>Vous pouvez retourner les articles non portés sous 30 jours, à condition qu’ils soient dans leur état et leur emballage d’origine.</p>
                            </div>

                            <div>
                                <p class="text-black font-medium mb-3">Assistance</p>
                                <p>Pour toute question concernant les tailles, la livraison ou l’entretien, contactez Maison Plush Paris avant de passer commande.</p>
                            </div>
                        @endif
                    </div>
                </article>

               @if($product->reviews()->count() > 0)
                <article x-show="tab === 'reviews'" x-cloak>
                    <div class="flex items-end justify-between gap-8 mb-10">
                        <div>
                            <h2 class="text-2xl md:text-3xl font-medium mb-3">
                                {{ __('Customer Reviews') }}
                            </h2>

                            <div class="flex items-center gap-3">
                                <div class="flex gap-1 text-black">
                                    @for($i = 0; $i < 5; $i++)
                                        <span>★</span>
                                    @endfor
                                </div>
                                <p class="text-sm text-neutral-500"> {{ $product->reviews()->avg('rating') }} / 5 {{ __('based on early feedback') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-6 md:grid-cols-2">
                        @forelse($product->reviews as $review)
                            <article class="flex flex-col rounded-2xl bg-neutral-50 p-6">
                                <div class="mb-4 flex gap-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <x-tabler-star-filled
                                            class="h-4 w-4 {{ $i <= $review->rating ? 'text-amber-500' : 'text-neutral-200' }}"
                                        />
                                    @endfor
                                </div>

                                <p class="mb-6 flex-1 whitespace-pre-line text-sm text-neutral-600">
                                    {{ $review->review }}
                                </p>

                                <footer class="text-sm">
                                    <p class="font-medium text-black">
                                        {{ $review->name }}
                                    </p>

                                    <p class="mt-1 text-neutral-400">
                                        {{ $review->created_at->format('d.m.Y') }}
                                    </p>
                                </footer>
                            </article>
                        @empty
                            <p class="text-neutral-500">
                                {{ __('No reviews yet.') }}
                            </p>
                        @endforelse
                    </div>

                </article>
                @endif
            </div>
        </div>
    </section>

    @if($similar->isNotEmpty())
        <section class="container my-24">
            <h2 class="section_title mb-10">{{ __('You Might Also Like') }}</h2>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($similar as $item)
                    @include('shop._card', ['product' => $item])
                @endforeach
            </div>
        </section>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            window.metaTrack?.('ViewContent', {
                content_type: 'product',
                content_ids: [@js((string) $product->id)],
                content_name: @js($product->title),
                content_category: @js($collectionName ?? ''),
                value: {{ (float) $product->price }},
                currency: 'EUR',
            });
        });
    </script>

</x-layout>
