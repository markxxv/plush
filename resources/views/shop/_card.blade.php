<article
    class="products_item group relative"
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
    <a href="{{ localized_route('shop.item', ['url' => $product->url]) }}" class="absolute inset-0 z-10"></a>

    <div x-ref="imgBox" class="img relative overflow-hidden">
        @php
            $images = $product->media;
            $firstMedia = $images->get(0);
            $secondMedia = $images->get(1) ?? $firstMedia;

            $firstImage = $firstMedia
                ? ($firstMedia->hasGeneratedConversion('small') ? $firstMedia->getUrl('small') : $firstMedia->getUrl())
                : null;

            $secondImage = $secondMedia
                ? ($secondMedia->hasGeneratedConversion('small') ? $secondMedia->getUrl('small') : $secondMedia->getUrl())
                : $firstImage;

            $firstAvif = $firstMedia?->hasGeneratedConversion('smalla')
                ? $firstMedia->getUrl('smalla')
                : null;

            $secondAvif = $secondMedia?->hasGeneratedConversion('smalla')
                ? $secondMedia->getUrl('smalla')
                : null;

            $wishlistImage = $firstMedia
                ? ($firstMedia->hasGeneratedConversion('thumb') ? $firstMedia->getUrl('thumb') : $firstMedia->getUrl())
                : null;
        @endphp

        <picture class="block">
            @if($firstAvif)
                <source srcset="{{ $firstAvif }}" type="image/avif">
            @endif

            <img
                src="{{ $firstImage }}"
                width="800"
                height="1199"
                loading="lazy"
                decoding="async"
                class="first w-full block"
                alt="{{ $product->title }}"
            >
        </picture>

        {{-- Liquid reveal: second image spreads from cursor position like a drop of water --}}
        <picture class="block">
            @if($secondAvif)
                <source media="(min-width: 768px)" srcset="{{ $secondAvif }}" type="image/avif">
            @endif

            <source media="(min-width: 768px)" srcset="{{ $secondImage }}">

            <img
                src="{{ $firstImage }}"
                width="800"
                height="1199"
                loading="lazy"
                decoding="async"
                class="hover w-full absolute inset-0 object-cover transition-[clip-path] duration-[900ms] ease-out"
                :style="`clip-path: circle(${hover ? 150 : 0}% at ${x}% ${y}%)`"
                alt="{{ $product->title }}"
            >
        </picture>

        {{-- Floating cursor tag --}}
        <div
            class="pointer-events-none absolute z-20 hidden md:flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-black text-white text-[10px] uppercase tracking-[0.15em] transition-opacity duration-300 whitespace-nowrap"
            :class="hover ? 'opacity-100' : 'opacity-0'"
            :style="`left:${x}%; top:${y}%; transform: translate(-50%, -160%)`"
        >
            {{ __('view') }}
            <x-tabler-arrow-up-right class="w-3 h-3" stroke-width="1.5" />
        </div>

        {{-- Floating action buttons --}}
        <div class="absolute top-3 right-3 z-20 flex flex-col gap-2">
            <button
                type="button"
                onclick="event.preventDefault(); Alpine.store('wishlist').toggleItem({{ json_encode([
                    'id' => $product->id,
                    'title' => $product->title,
                    'price' => $product->price,
                    'image' => $wishlistImage,
                    'url' => $product->url
                ]) }})"
                x-data
                class="w-10 h-10 rounded-full bg-white shadow-sm flex items-center justify-center opacity-0 scale-75 group-hover:opacity-100 group-hover:scale-100 transition-all duration-300 hover:bg-black"
                style="transition-delay: 60ms"
            >
                <template x-if="$store.wishlist.items.some(item => item.id === {{ $product->id }})">
                    <x-tabler-heart-filled class="w-4 h-4 text-rose-500" stroke-width="1.3" />
                </template>
                <template x-if="!$store.wishlist.items.some(item => item.id === {{ $product->id }})">
                    <x-tabler-heart class="w-4 h-4 text-zinc-600 group-hover/btn:text-white" stroke-width="1.3" />
                </template>
            </button>

            <span
                class="w-10 h-10 rounded-full bg-white shadow-sm flex items-center justify-center opacity-0 scale-75 group-hover:opacity-100 group-hover:scale-100 transition-all duration-300 hover:bg-black"
                style="transition-delay: 140ms"
            >
                <x-tabler-search class="w-4 h-4 text-zinc-600" stroke-width="1.3" />
            </span>
        </div>
    </div>

    <footer class="products_footer mt-3">
        <p class="product_title transition-colors duration-500 group-hover:text-black">
            {{ $product->title }}
        </p>
        <p class="product_title flex items-center gap-2 text-[11px] uppercase text-neutral-400">
            <span class="w-1.5 h-1.5 rounded-full bg-neutral-300 group-hover:bg-black transition-all duration-500"></span>
            {{ __('details') }}
        </p>
    </footer>
</article>
