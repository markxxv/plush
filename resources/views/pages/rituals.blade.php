@php
    $fr = app()->getLocale() === 'fr';
    $description = trim(strip_tags($collection->description ?: ''));
    $metaTitle = $collection->meta_title ?: 'PLUSH Rituals — Maison Plush Paris';
    $metaDescription = $collection->meta_description ?: $description;
    $rituals = [
        [
            'number' => '01',
            'name' => 'Rainfall Forest',
            'notes' => $fr ? 'Feuilles humides · Pin · Terre' : 'Wet leaves · Pine · Earth',
            'story' => $fr
                ? 'Le silence d’une forêt après la pluie. Un parfum végétal et boisé, à retrouver sur la peau et dans son intérieur.'
                : 'The stillness of a forest after rainfall. A green, woody fragrance to carry on your skin and into your home.',
            'slugs' => ['hand-cream-rainfall-forest', 'diffuser-rainfall-forest'],
            'tone' => 'forest',
        ],
        [
            'number' => '02',
            'name' => 'Petrichor',
            'notes' => $fr ? 'Pierre humide · Air frais · Accord minéral' : 'Wet stone · Fresh air · Mineral accord',
            'story' => $fr
                ? 'Les premières gouttes de pluie sur la pierre. Un parfum frais et minéral qui invite à retrouver un instant de calme.'
                : 'The first drops of rain on stone. A clean, mineral fragrance that invites a moment of stillness.',
            'slugs' => ['hand-cream-petrichor', 'diffuser-petrichor'],
            'tone' => 'stone',
        ],
    ];
@endphp

@section('metaTitle', $metaTitle)
@section('metaDescription', $metaDescription)
@section('body_class', ' rituals_page')
@section('head')
    <link rel="preload" as="image" href="/img/poster.webp" fetchpriority="high">
    <link rel="canonical" href="{{ localized_route('rituals') }}">
    <link rel="alternate" hreflang="en" href="{{ localized_route('rituals', [], 'en') }}">
    <link rel="alternate" hreflang="fr" href="{{ localized_route('rituals', [], 'fr') }}">
    <style>
        .rituals_page .site_header:not(:hover) { color: #fff; }
        .rituals { background: #f8f7f4; color: #222521; font-weight: 300; }
        .rituals * { box-sizing: border-box; }
        .rituals-hero { position: relative; isolation: isolate; min-height: 560px; min-height: min(780px, max(520px, 80svh)); display: grid; place-items: center; overflow: hidden; color: white; background: #171e1b url('/img/poster.webp') center/cover no-repeat; }
        .rituals-hero video, .rituals-hero::after { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; z-index: -1; }
        .rituals-hero::after { content: ''; background: linear-gradient(180deg, rgba(0,0,0,.36), rgba(0,0,0,.22) 38%, rgba(0,0,0,.55)); pointer-events: none; }
        .rituals-hero-content { padding: 112px 24px 80px; text-align: center; max-width: 680px; }
        .rituals-kicker { font-size: 11px; font-weight: 400; letter-spacing: .24em; text-transform: uppercase; }
        .rituals-hero h1 { margin: 18px 0 14px; font-size: 46px; letter-spacing: .09em; font-weight: 300; }
        .rituals-hero-subtitle { font-size: 19px; opacity: .92; }
        .rituals-hero-link { display: inline-flex; align-items: center; gap: 12px; margin-top: 36px; padding-bottom: 7px; border-bottom: 1px solid rgba(255,255,255,.7); font-size: 13px; font-weight: 400; letter-spacing: .04em; }
        .rituals-video-toggle { position: absolute; bottom: 20px; right: 24px; display: grid; place-items: center; width: 44px; height: 44px; color: white; background: rgba(0,0,0,.35); border-radius: 50%; }
        .rituals-intro { max-width: 850px; margin: auto; padding: 100px 24px 92px; text-align: center; }
        .rituals-intro .rituals-kicker { color: #70766e; }
        .rituals-intro h2 { font-weight: 300; font-size: 36px; margin: 18px 0 22px; }
        .rituals-intro-description { max-width: 680px; margin: auto; font-size: 19px; line-height: 1.65; color: #575d55; }
        .rituals-jump { display: flex; flex-wrap: wrap; justify-content: center; gap: 12px 28px; margin-top: 34px; font-size: 14px; font-weight: 400; }
        .rituals-jump a { padding: 6px 0; border-bottom: 1px solid #b6b9b0; }
        .rituals-chapter { padding: 70px 0; scroll-margin-top: 20px; }
        .rituals-chapter--forest { background: #e9ece5; }
        .rituals-chapter--stone { background: #efede9; }
        .rituals-grid { display: grid; gap: 40px; grid-template-columns: minmax(0, .85fr) minmax(0, 1.6fr); align-items: center; }
        .rituals-chapter--stone .rituals-grid { grid-template-columns: minmax(0, 1.6fr) minmax(0, .85fr); }
        .rituals-chapter--stone .rituals-story { order: 2; }
        .rituals-story { padding: 24px 20px; }
        .rituals-story .rituals-kicker { color: #646a61; }
        .rituals-story h2 { font-weight: 300; font-size: 36px; margin: 20px 0 12px; }
        .rituals-notes { font-size: 13px; font-weight: 400; letter-spacing: .02em; color: #646a61; }
        .rituals-story-copy { font-size: 19px; line-height: 1.6; margin: 24px 0 30px; max-width: 350px; }
        .rituals-pair { display: inline-flex; gap: 16px; align-items: center; justify-content: space-between; padding: 14px 20px; min-height: 48px; background: #242b24; color: white; border-radius: 3px; font-size: 14px; font-weight: 400; transition: background .25s; }
        .rituals-pair:hover { background: #414b40; }
        .rituals-pair-price { white-space: nowrap; opacity: .85; }
        .rituals-products { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 20px; }
        .rituals-product { min-width: 0; display: flex; flex-direction: column; }
        .rituals-product-image { display: block; position: relative; aspect-ratio: 2 / 3; overflow: hidden; background: #e0dfdb; }
        .rituals-product-image picture { display: block; width: 100%; height: 100%; }
        .rituals-product-image img { width: 100%; height: 100%; object-fit: cover; transition: transform .7s, opacity .5s; }
        .rituals-product-image .rituals-hover-image { position: absolute; inset: 0; opacity: 0; }
        .rituals-product-body { padding: 18px 0 0; display: flex; flex-direction: column; flex: 1; }
        .rituals-product-title { font-weight: 400; font-size: 18px; margin-bottom: 4px; }
        .rituals-product-title:hover { text-decoration: underline; text-underline-offset: 3px; }
        .rituals-product-meta { display: flex; gap: 10px; justify-content: space-between; align-items: baseline; margin: 8px 0 14px; font-size: 15px; color: #555950; }
        .rituals-product-price { font-size: 18px; font-weight: 400; white-space: nowrap; color: #222521; }
        .rituals-product-action { display: flex; margin-top: auto; justify-content: space-between; align-items: center; gap: 5px; width: 100%; min-height: 46px; padding: 10px 14px; background: #fff; border-radius: 3px; color: #242b24; font-size: 13px; font-weight: 400; text-align: left; transition: background .25s, color .25s; }
        .rituals-product-action:hover { background: #242b24; color: #fff; }
        .rituals-product-action svg { flex-shrink: 0; }
        .rituals-out { margin-top: auto; font-size: 13px; color: #646a61; padding: 14px 0; }
        .rituals-out:hover { text-decoration: underline; }
        .rituals-end { text-align: center; padding: 86px 24px; background: #f8f7f4; }
        .rituals-end h2 { font-size: 32px; font-weight: 300; margin: 16px 0; }
        .rituals-end a { font-size: 14px; display: inline-flex; align-items: center; gap: 8px; border-bottom: 1px solid #7d847a; padding-bottom: 5px; }
        @media (hover: hover) {
            .rituals-product-image:hover img { transform: scale(1.025); }
            .rituals-product-image:hover .rituals-hover-image { opacity: 1; }
        }
        @media(min-width: 1200px) {
            .rituals-grid { gap: 72px; padding-inline: 28px; }
            .rituals-chapter { padding: 84px 0; }
            .rituals-story { padding: 30px 40px; }
        }
        @media(max-width: 767px) {
            .rituals-hero { min-height: 520px; min-height: min(640px, max(500px, 75svh)); }
            .rituals-hero h1 { font-size: 36px; }
            .rituals-hero-subtitle { font-size: 17px; }
            .rituals-intro { padding: 64px 24px; }
            .rituals-intro h2, .rituals-story h2 { font-size: 30px; }
            .rituals-intro-description, .rituals-story-copy { font-size: 17px; }
            .rituals-chapter { padding: 40px 0; }
            .rituals-grid, .rituals-chapter--stone .rituals-grid { grid-template-columns: 1fr; gap: 24px; }
            .rituals-chapter--stone .rituals-story { order: 0; }
            .rituals-story { padding: 0 4px; }
            .rituals-story-copy { max-width: 100%; margin: 18px 0 24px; }
            .rituals-products { gap: 10px; }
            .rituals-product-body { padding-top: 14px; }
            .rituals-product-title { font-size: 16px; }
            .rituals-product-meta { font-size: 13px; }
            .rituals-product-price { font-size: 16px; }
            .rituals-product-action { padding-inline: 10px; font-size: 12px; }
            .rituals-end { padding: 60px 24px; }
        }
        @media(prefers-reduced-motion: reduce) {
            .rituals *, .rituals *::before, .rituals *::after { transition: none !important; }
        }
    </style>
@endsection

<x-layout>
    <main class="rituals">
        <section class="rituals-hero" x-data="{
            playing: false,
            start() {
                if (!this.$refs.video.getAttribute('src')) this.$refs.video.src = '/img/main_products.mp4';
                this.$refs.video.play().catch(() => {});
            },
            init() {
                if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                    this.$nextTick(() => window.requestAnimationFrame(() => this.start()));
                }
            }
        }">
            <video x-ref="video" @play="playing = true" @pause="playing = false" muted loop playsinline preload="none" poster="/img/poster.webp" aria-hidden="true"></video>
            <div class="rituals-hero-content">
                <p class="rituals-kicker">Maison Plush Paris</p>
                <h1>PLUSH Rituals</h1>
                <p class="rituals-hero-subtitle">{{ $fr ? 'Pour soi, pour son intérieur.' : 'For yourself, for your space.' }}</p>
                <a href="#rituals" class="rituals-hero-link">
                    {{ $fr ? 'Découvrir les rituels' : 'Discover the rituals' }}
                    <x-tabler-arrow-down class="size-4" stroke-width="1.4" />
                </a>
            </div>
            <button type="button" class="rituals-video-toggle" @click="playing ? $refs.video.pause() : start()" :aria-label="playing ? @js($fr ? 'Mettre la vidéo en pause' : 'Pause video') : @js($fr ? 'Lire la vidéo' : 'Play video')">
                <span x-show="playing" x-cloak><x-tabler-player-pause class="size-5" stroke-width="1.5" /></span>
                <span x-show="!playing"><x-tabler-player-play class="size-5" stroke-width="1.5" /></span>
            </button>
        </section>

        <section id="rituals" class="rituals-intro">
            <p class="rituals-kicker">{{ $fr ? 'Le rituel quotidien' : 'The daily ritual' }}</p>
            <h2>{{ $fr ? 'Deux fragrances. Deux gestes.' : 'Two scents. Two gestures.' }}</h2>
            @if($description !== '')
                <p class="rituals-intro-description">{{ $description }}</p>
            @endif
            <nav class="rituals-jump" aria-label="{{ $fr ? 'Fragrances' : 'Scents' }}">
                @foreach($rituals as $ritual)
                    @if(collect($ritual['slugs'])->contains(fn ($slug) => $products->has($slug)))
                        <a href="#{{ $ritual['tone'] }}">{{ $ritual['name'] }} &darr;</a>
                    @endif
                @endforeach
            </nav>
        </section>

        @foreach($rituals as $ritual)
            @php
                $items = collect($ritual['slugs'])->map(fn ($slug) => $products->get($slug))->filter()->values();
                $cartProducts = $items->mapWithKeys(function ($product) use ($collection) {
                    $media = $product->media->first();
                    return [$product->id => [
                        'id' => $product->id,
                        'title' => $product->title,
                        'price' => (float) $product->price,
                        'image' => $media ? ($media->hasGeneratedConversion('thumb') ? $media->getUrl('thumb') : $media->getUrl()) : null,
                        'url' => localized_route('shop.item', ['url' => $product->url]),
                        'category' => $collection->title,
                        'sizes' => $product->sizes->map(fn ($size) => ['id' => $size->id, 'title' => $size->value])->values(),
                    ]];
                });
                $canBuyPair = $items->count() === 2 && $items->every(fn ($product) => $product->availability && !$product->preorder && $product->price > 0 && $product->sizes->count() <= 1);
            @endphp

            @continue($items->isEmpty())

            <section id="{{ $ritual['tone'] }}" class="rituals-chapter rituals-chapter--{{ $ritual['tone'] }}">
                <div class="container rituals-grid">
                    <div class="rituals-story">
                        <p class="rituals-kicker">{{ $fr ? 'Rituel' : 'Ritual' }} / {{ $ritual['number'] }}</p>
                        <h2>{{ $ritual['name'] }}</h2>
                        <p class="rituals-notes">{{ $ritual['notes'] }}</p>
                        <p class="rituals-story-copy">{{ $ritual['story'] }}</p>
                        @if($canBuyPair)
                            <button type="button" x-data="{ products: @js($cartProducts->values()) }" @click="products.forEach(product => $store.cart.addItem(product, null, product.sizes[0]?.id ?? null)); $store.cart.isOpen = true" class="rituals-pair">
                                {{ $fr ? 'Adopter le rituel' : 'Shop the full ritual' }}
                                <span class="rituals-pair-price">{{ number_format((float) $items->sum('price'), fmod((float) $items->sum('price'), 1) === 0.0 ? 0 : 2, $fr ? ',' : '.', ' ') }} €</span>
                            </button>
                        @endif
                    </div>
                    <div class="rituals-products">
                        @foreach($items as $product)
                            @php
                                $media = $product->media->first();
                                $hover = $product->media->get(1);
                                $image = $media ? ($media->hasGeneratedConversion('small') ? $media->getUrl('small') : $media->getUrl()) : null;
                                $hoverImage = $hover ? ($hover->hasGeneratedConversion('small') ? $hover->getUrl('small') : $hover->getUrl()) : null;
                                $url = localized_route('shop.item', ['url' => $product->url]);
                            @endphp
                            <article class="rituals-product">
                                <a href="{{ $url }}" class="rituals-product-image" aria-label="{{ $product->title }}">
                                    @if($image)
                                        <picture>
                                            @if($media->hasGeneratedConversion('smalla'))
                                                <source srcset="{{ $media->getUrl('smalla') }}" type="image/avif">
                                            @endif
                                            <img src="{{ $image }}" alt="{{ $product->title }}" width="800" height="1199" loading="lazy" decoding="async">
                                        </picture>
                                    @endif
                                    @if($hoverImage)
                                        <img class="rituals-hover-image" src="{{ $hoverImage }}" width="800" height="1199" alt="" loading="lazy" decoding="async">
                                    @endif
                                </a>
                                <div class="rituals-product-body">
                                    <h3 class="rituals-product-title"><a href="{{ $url }}">{{ $product->title }}</a></h3>
                                    <div class="rituals-product-meta">
                                        <span>{{ $product->volume > 1 ? $product->volume . ' ml' : '' }}</span>
                                        @if($product->price > 0)
                                            <span class="rituals-product-price">{{ number_format((float) $product->price, fmod((float) $product->price, 1) === 0.0 ? 0 : 2, $fr ? ',' : '.', ' ') }} €</span>
                                        @endif
                                    </div>
                                    @if($product->availability && !$product->preorder && $product->price > 0 && $product->sizes->count() <= 1)
                                        <button type="button" x-data="{ product: @js($cartProducts->get($product->id)) }" @click="$store.cart.addItem(product, null, product.sizes[0]?.id ?? null)" class="rituals-product-action">
                                            {{ $fr ? 'Ajouter au panier' : 'Add to bag' }}
                                            <x-tabler-plus class="size-4" stroke-width="1.4" />
                                        </button>
                                    @elseif(!$product->availability && !$product->preorder)
                                        <a href="{{ $url }}" class="rituals-out">{{ $fr ? 'Rupture de stock' : 'Out of stock' }}</a>
                                    @else
                                        <a href="{{ $url }}" class="rituals-product-action">
                                            {{ $product->preorder ? ($fr ? 'Précommander' : 'Pre-order') : ($fr ? 'Voir le produit' : 'View product') }}
                                            <x-tabler-arrow-up-right class="size-4" stroke-width="1.4" />
                                        </a>
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>
        @endforeach

        <section class="rituals-end">
            <p class="rituals-kicker">PLUSH Rituals</p>
            <h2>{{ $fr ? 'Un instant à soi.' : 'A moment for yourself.' }}</h2>
            <a href="#rituals">{{ $fr ? 'Retrouver mon rituel' : 'Find your ritual' }} <x-tabler-arrow-up class="size-4" stroke-width="1.4" /></a>
        </section>
    </main>
</x-layout>
