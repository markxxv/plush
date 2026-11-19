@php
    $fr = app()->getLocale() === 'fr';
    $description = trim(strip_tags($collection->description ?: ''));
    $metaTitle = $collection->meta_title ?: 'PLUSH Rituals — Maison Plush Paris';
    $metaDescription = $collection->meta_description ?: $description;
    $money = fn ($value) => number_format((float) $value, fmod((float) $value, 1) === 0.0 ? 0 : 2, $fr ? ',' : '.', ' ') . ' €';
    $typeFor = fn (string $slug) => match (true) {
        str_starts_with($slug, 'diffuser') => $fr ? 'Diffuseur' : 'Diffuser',
        str_starts_with($slug, 'hand-cream') => $fr ? 'Crème pour les mains' : 'Hand cream',
        default => null,
    };
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
        .rituals { --ease: cubic-bezier(.22, .61, .36, 1); --ink: #171717; --muted: #737373; --line: #e5e5e5; background: #fff; color: var(--ink); font-weight: 300; text-rendering: optimizeLegibility; }
        .rituals * { box-sizing: border-box; }
        .rituals ::selection { background: #171717; color: #fff; }
        .rituals :focus-visible { outline: 1px solid currentColor; outline-offset: 5px; border-radius: 2px; }
        .rituals h1, .rituals h2 { text-wrap: balance; }
        .rituals p { text-wrap: pretty; }
        .rituals-kicker { font-size: 11px; font-weight: 400; letter-spacing: .26em; text-transform: uppercase; }

        /* Hero */
        .rituals-hero { position: relative; isolation: isolate; min-height: 560px; min-height: min(780px, max(520px, 80svh)); display: grid; place-items: center; overflow: hidden; color: white; background: #171717 url('/img/poster.webp') center/cover no-repeat; }
        .rituals-hero video, .rituals-hero::after { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; z-index: -1; }
        .rituals-hero::after { content: ''; background: radial-gradient(ellipse at 50% 46%, rgba(0,0,0,.08), rgba(0,0,0,.34) 100%), linear-gradient(180deg, rgba(0,0,0,.42), rgba(0,0,0,.14) 36%, rgba(0,0,0,.6)); pointer-events: none; }
        .rituals-hero-content { padding: 112px 24px 96px; text-align: center; max-width: 680px; text-shadow: 0 1px 28px rgba(0,0,0,.22); }
        .rituals-hero .rituals-kicker { display: inline-flex; align-items: center; gap: 16px; }
        .rituals-hero .rituals-kicker::before, .rituals-hero .rituals-kicker::after { content: ''; width: 28px; height: 1px; background: currentColor; opacity: .55; }
        .rituals-hero h1 { margin: 22px 0 16px; font-size: clamp(36px, 4.6vw, 54px); line-height: 1.08; letter-spacing: .1em; font-weight: 300; }
        .rituals-hero-subtitle { font-size: 19px; letter-spacing: .02em; opacity: .92; }
        .rituals-hero-link { display: inline-flex; align-items: center; gap: 12px; margin-top: 40px; padding-bottom: 8px; font-size: 13px; font-weight: 400; letter-spacing: .06em; background: linear-gradient(rgba(255,255,255,.75), rgba(255,255,255,.75)) left bottom / 100% 1px no-repeat; transition: background-size .5s var(--ease), gap .4s var(--ease); }
        .rituals-hero-link svg { transition: transform .4s var(--ease); }
        .rituals-hero-link:hover { background-size: 0 1px; background-position: right bottom; }
        .rituals-hero-link:hover svg { transform: translateY(3px); }
        .rituals-hero-scroll { position: absolute; left: 50%; bottom: 0; width: 1px; height: 56px; overflow: hidden; background: rgba(255,255,255,.2); pointer-events: none; }
        .rituals-hero-scroll::after { content: ''; position: absolute; inset: 0; background: #fff; transform: translateY(-100%); animation: rituals-scroll 2.6s var(--ease) infinite; }
        .rituals-video-toggle { position: absolute; bottom: 20px; right: 24px; display: grid; place-items: center; width: 44px; height: 44px; color: white; background: rgba(0,0,0,.28); border: 1px solid rgba(255,255,255,.28); border-radius: 50%; -webkit-backdrop-filter: blur(10px); backdrop-filter: blur(10px); transition: background .3s, border-color .3s, transform .3s var(--ease); }
        .rituals-video-toggle:hover { background: rgba(0,0,0,.5); border-color: rgba(255,255,255,.55); transform: scale(1.06); }
        .rituals-video-toggle:focus-visible { outline-color: #fff; outline-offset: 3px; border-radius: 50%; }
        .rituals-hero .rituals-hero-link:focus-visible { outline-color: #fff; }

        /* Intro */
        .rituals-intro { max-width: 850px; margin: auto; padding: 0 24px 92px; text-align: center; }
        .rituals-intro::before { content: ''; display: block; width: 1px; height: 64px; margin: 0 auto 44px; background: linear-gradient(#fff, #a3a3a3); }
        .rituals-intro .rituals-kicker { color: var(--muted); }
        .rituals-intro h2 { font-weight: 300; font-size: 36px; line-height: 1.2; margin: 18px 0 22px; }
        .rituals-intro-description { max-width: 680px; margin: auto; font-size: 19px; line-height: 1.7; color: #525252; }
        .rituals-jump { display: flex; flex-wrap: wrap; justify-content: center; gap: 10px; margin-top: 38px; font-size: 14px; font-weight: 400; }
        .rituals-jump a { display: inline-flex; align-items: center; gap: 10px; padding: 9px 22px 9px 16px; border: 1px solid var(--line); border-radius: 999px; color: #525252; background: #fff; transition: color .3s, background .3s, border-color .3s; }
        .rituals-jump a span { font-size: 11px; letter-spacing: .12em; color: #a3a3a3; transition: color .3s; }
        .rituals-jump a:hover { color: #fff; background: #000; border-color: #000; }
        .rituals-jump a:hover span { color: rgba(255,255,255,.6); }

        /* Chapters */
        .rituals-chapter { padding: 70px 0; scroll-margin-top: 20px; }
        .rituals-chapter--forest { background: #f4f5f2; }
        .rituals-chapter--stone { background: #f6f6f7; }
        .rituals-grid { display: grid; gap: 40px; grid-template-columns: minmax(0, .85fr) minmax(0, 1.6fr); align-items: center; }
        .rituals-chapter--stone .rituals-grid { grid-template-columns: minmax(0, 1.6fr) minmax(0, .85fr); }
        .rituals-chapter--stone .rituals-story { order: 2; }
        .rituals-story { position: relative; padding: 24px 20px; }
        .rituals-numeral { position: absolute; top: -78px; left: 6px; z-index: 0; font-size: 190px; line-height: 1; font-weight: 200; letter-spacing: -.04em; color: rgba(0,0,0,.045); pointer-events: none; user-select: none; }
        .rituals-story > *:not(.rituals-numeral) { position: relative; z-index: 1; }
        .rituals-story .rituals-kicker { display: flex; align-items: center; gap: 14px; color: var(--muted); }
        .rituals-story .rituals-kicker::after { content: ''; width: 40px; height: 1px; background: currentColor; opacity: .45; }
        .rituals-story h2 { font-weight: 300; font-size: 38px; line-height: 1.1; letter-spacing: .02em; margin: 22px 0 14px; }
        .rituals-notes { display: flex; flex-wrap: wrap; align-items: center; row-gap: 4px; margin: 0; padding: 0; list-style: none; font-size: 13px; font-weight: 400; letter-spacing: .03em; color: var(--muted); }
        .rituals-notes li + li::before { content: ''; display: inline-block; width: 3px; height: 3px; margin: 0 12px; vertical-align: middle; border-radius: 50%; background: #a3a3a3; }
        .rituals-story-copy { font-size: 19px; line-height: 1.65; margin: 26px 0 32px; max-width: 350px; color: #404040; }
        .rituals-pair-wrap { display: inline-flex; flex-direction: column; align-items: flex-start; gap: 12px; }
        .rituals-pair { display: inline-flex; gap: 18px; align-items: center; justify-content: space-between; padding: 16px 20px; min-height: 48px; background: #000; color: white; border-radius: 12px; font-size: 14px; font-weight: 400; transition: background .3s, transform .4s var(--ease), box-shadow .4s var(--ease); }
        .rituals-pair:hover { background: #262626; transform: translateY(-2px); box-shadow: 0 12px 28px -14px rgba(0,0,0,.45); }
        .rituals-pair:active { transform: translateY(0); box-shadow: none; }
        .rituals-pair-price { white-space: nowrap; padding-left: 18px; border-left: 1px solid rgba(255,255,255,.28); font-variant-numeric: tabular-nums; opacity: .9; }
        .rituals-pair-note { padding-left: 2px; font-size: 12px; font-weight: 400; letter-spacing: .04em; color: var(--muted); }

        /* Products */
        .rituals-products { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 20px; }
        .rituals-product { min-width: 0; display: flex; flex-direction: column; }
        .rituals-product-image { display: block; position: relative; aspect-ratio: 2 / 3; overflow: hidden; background: #e5e5e5; }
        .rituals-product-image::after { content: ''; position: absolute; inset: 0; z-index: 2; box-shadow: inset 0 0 0 1px rgba(0,0,0,.05); pointer-events: none; }
        .rituals-product-image picture { display: block; width: 100%; height: 100%; }
        .rituals-product-image img { width: 100%; height: 100%; object-fit: cover; transition: transform 1.1s var(--ease), opacity .6s var(--ease); }
        .rituals-product-image .rituals-hover-image { position: absolute; inset: 0; opacity: 0; }
        .rituals-product-more { position: absolute; right: 14px; bottom: 14px; z-index: 3; display: grid; place-items: center; width: 40px; height: 40px; color: #171717; background: rgba(255,255,255,.88); border-radius: 50%; -webkit-backdrop-filter: blur(8px); backdrop-filter: blur(8px); opacity: 0; transform: translateY(8px); transition: opacity .4s var(--ease), transform .4s var(--ease); pointer-events: none; }
        .rituals-product-body { padding: 20px 0 0; display: flex; flex-direction: column; flex: 1; }
        .rituals-product-type { display: block; margin-bottom: 8px; font-size: 11px; font-weight: 400; letter-spacing: .2em; text-transform: uppercase; color: var(--muted); }
        .rituals-product-title { font-weight: 400; font-size: 18px; line-height: 1.3; margin-bottom: 4px; }
        .rituals-product-title a { background: linear-gradient(currentColor, currentColor) left bottom / 0 1px no-repeat; transition: background-size .5s var(--ease); }
        .rituals-product-title a:hover { background-size: 100% 1px; }
        .rituals-product-meta { display: flex; gap: 10px; justify-content: space-between; align-items: baseline; margin: 10px 0 18px; padding-top: 12px; border-top: 1px solid rgba(0,0,0,.09); font-size: 15px; color: #525252; }
        .rituals-product-price { font-size: 18px; font-weight: 400; white-space: nowrap; color: var(--ink); font-variant-numeric: tabular-nums; }
        .rituals-product-action { display: flex; margin-top: auto; justify-content: center; align-items: center; gap: 10px; width: 100%; min-height: 48px; padding: 16px 14px; background: #000; border-radius: 12px; color: #fff; font-size: 14px; font-weight: 400; text-align: center; transition: background .3s, color .3s, transform .4s var(--ease), box-shadow .4s var(--ease); }
        .rituals-product-action:hover { background: #262626; color: #fff; transform: translateY(-2px); box-shadow: 0 12px 28px -14px rgba(0,0,0,.45); }
        .rituals-product-action:active { transform: translateY(0); box-shadow: none; }
        .rituals-product-action--secondary { background: #fff; color: #171717; box-shadow: 0 1px 2px rgb(0 0 0 / .05), inset 0 0 0 1px rgba(0,0,0,.06); }
        .rituals-product-action--secondary:hover { background: #171717; color: #fff; box-shadow: 0 12px 28px -14px rgba(0,0,0,.45); }
        .rituals-product-action svg { flex-shrink: 0; transition: transform .4s var(--ease); }
        .rituals-product-action:hover svg { transform: translate(1px, -1px); }
        .rituals-product-action:not(.rituals-product-action--secondary) svg { color: #a3a3a3; }
        .rituals-out { margin-top: auto; font-size: 13px; color: var(--muted); padding: 14px 0; }
        .rituals-out:hover { text-decoration: underline; text-underline-offset: 3px; }

        /* Closing */
        .rituals-end { position: relative; text-align: center; padding: 20px 24px 104px; background: #fff; }
        .rituals-end::before { content: ''; display: block; width: 1px; height: 64px; margin: 0 auto 48px; background: linear-gradient(#fff, #a3a3a3); }
        .rituals-end .rituals-kicker { color: var(--muted); }
        .rituals-end h2 { font-size: 34px; line-height: 1.2; font-weight: 300; letter-spacing: .02em; margin: 18px 0 30px; }
        .rituals-end-link { display: inline-flex; align-items: center; gap: 10px; padding: 11px 22px; border: 1px solid var(--line); border-radius: 999px; font-size: 14px; font-weight: 400; color: #525252; transition: color .3s, background .3s, border-color .3s; }
        .rituals-end-link svg { transition: transform .4s var(--ease); }
        .rituals-end-link:hover { color: #fff; background: #000; border-color: #000; }
        .rituals-end-link:hover svg { transform: translateY(-2px); }

        @keyframes rituals-rise { from { opacity: 0; transform: translateY(18px); } to { opacity: 1; transform: none; } }
        @keyframes rituals-scroll { 0% { transform: translateY(-100%); } 55%, 100% { transform: translateY(100%); } }

        @media (hover: hover) {
            .rituals-product-image:hover img { transform: scale(1.035); }
            .rituals-product-image:hover .rituals-hover-image { opacity: 1; }
            .rituals-product-image:hover .rituals-product-more { opacity: 1; transform: none; }
        }
        @media (prefers-reduced-motion: no-preference) {
            .rituals-hero-content > * { animation: rituals-rise 1.1s var(--ease) both; }
            .rituals-hero-content > :nth-child(2) { animation-delay: .12s; }
            .rituals-hero-content > :nth-child(3) { animation-delay: .24s; }
            .rituals-hero-content > :nth-child(4) { animation-delay: .38s; }
            .rituals.is-js [data-reveal] { opacity: 0; transform: translateY(22px); transition: opacity 1s var(--ease) var(--d, 0s), transform 1s var(--ease) var(--d, 0s); }
            .rituals.is-js [data-reveal].is-visible { opacity: 1; transform: none; }
        }
        @media(min-width: 1200px) {
            .rituals-grid { gap: 72px; padding-inline: 28px; }
            .rituals-chapter { padding: 96px 0; }
            .rituals-story { padding: 30px 40px; }
            .rituals-numeral { left: 24px; }
            .rituals-products { gap: 28px; }
        }
        @media(max-width: 767px) {
            .rituals-hero { min-height: 520px; min-height: min(640px, max(500px, 75svh)); }
            .rituals-hero-content { padding-bottom: 88px; }
            .rituals-hero-subtitle { font-size: 17px; }
            .rituals-hero-scroll { height: 44px; }
            .rituals-intro { padding: 0 24px 64px; }
            .rituals-intro::before, .rituals-end::before { height: 48px; margin-bottom: 32px; }
            .rituals-intro h2, .rituals-story h2 { font-size: 30px; }
            .rituals-intro-description, .rituals-story-copy { font-size: 17px; }
            .rituals-chapter { padding: 48px 0; }
            .rituals-grid, .rituals-chapter--stone .rituals-grid { grid-template-columns: 1fr; gap: 28px; }
            .rituals-chapter--stone .rituals-story { order: 0; }
            .rituals-story { padding: 0 4px; }
            .rituals-numeral { top: -48px; right: 0; left: auto; font-size: 112px; }
            .rituals-story-copy { max-width: 100%; margin: 18px 0 24px; }
            .rituals-pair-wrap { display: flex; width: 100%; }
            .rituals-pair { width: 100%; }
            .rituals-products { gap: 10px; }
            .rituals-product-body { padding-top: 16px; }
            .rituals-product-type { font-size: 10px; letter-spacing: .16em; }
            .rituals-product-title { font-size: 16px; }
            .rituals-product-meta { margin: 8px 0 14px; padding-top: 10px; font-size: 13px; }
            .rituals-product-price { font-size: 16px; }
            .rituals-product-action { padding-inline: 10px; font-size: 12px; }
            .rituals-product-more { display: none; }
            .rituals-end { padding: 8px 24px 72px; }
            .rituals-end h2 { font-size: 28px; }
        }
        @media(prefers-reduced-motion: reduce) {
            .rituals *, .rituals *::before, .rituals *::after { transition: none !important; animation: none !important; }
        }
    </style>
@endsection

<x-layout>
    <main class="rituals">
        <script>document.currentScript.parentElement.classList.add('is-js')</script>
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
            <span class="rituals-hero-scroll" aria-hidden="true"></span>
            <button type="button" class="rituals-video-toggle" @click="playing ? $refs.video.pause() : start()" :aria-label="playing ? @js($fr ? 'Mettre la vidéo en pause' : 'Pause video') : @js($fr ? 'Lire la vidéo' : 'Play video')">
                <span x-show="playing" x-cloak><x-tabler-player-pause class="size-5" stroke-width="1.5" /></span>
                <span x-show="!playing"><x-tabler-player-play class="size-5" stroke-width="1.5" /></span>
            </button>
        </section>

        <section id="rituals" class="rituals-intro" data-reveal>
            <p class="rituals-kicker">{{ $fr ? 'Le rituel quotidien' : 'The daily ritual' }}</p>
            <h2>{{ $fr ? 'Deux fragrances. Deux gestes.' : 'Two scents. Two gestures.' }}</h2>
            @if($description !== '')
                <p class="rituals-intro-description">{{ $description }}</p>
            @endif
            <nav class="rituals-jump" aria-label="{{ $fr ? 'Fragrances' : 'Scents' }}">
                @foreach($rituals as $ritual)
                    @if(collect($ritual['slugs'])->contains(fn ($slug) => $products->has($slug)))
                        <a href="#{{ $ritual['tone'] }}"><span>{{ $ritual['number'] }}</span>{{ $ritual['name'] }}</a>
                    @endif
                @endforeach
            </nav>
        </section>

        @foreach($rituals as $ritual)
            @php
                $items = collect($ritual['slugs'])->mapWithKeys(fn ($slug) => [$slug => $products->get($slug)])->filter();
                $noteList = array_values(array_filter(array_map('trim', explode('·', $ritual['notes']))));
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
                    <div class="rituals-story" data-reveal>
                        <span class="rituals-numeral" aria-hidden="true">{{ $ritual['number'] }}</span>
                        <p class="rituals-kicker">{{ $fr ? 'Rituel' : 'Ritual' }} / {{ $ritual['number'] }}</p>
                        <h2>{{ $ritual['name'] }}</h2>
                        <ul class="rituals-notes" aria-label="{{ $fr ? 'Notes olfactives' : 'Fragrance notes' }}">
                            @foreach($noteList as $note)
                                <li>{{ $note }}</li>
                            @endforeach
                        </ul>
                        <p class="rituals-story-copy">{{ $ritual['story'] }}</p>
                        @if($canBuyPair)
                            <div class="rituals-pair-wrap">
                                <button type="button" x-data="{ products: @js($cartProducts->values()) }" @click="products.forEach(product => $store.cart.addItem(product, null, product.sizes[0]?.id ?? null)); $store.cart.isOpen = true" class="rituals-pair">
                                    {{ $fr ? 'Adopter le rituel' : 'Shop the full ritual' }}
                                    <span class="rituals-pair-price">{{ $money($items->sum('price')) }}</span>
                                </button>
                                @if($pairNote = $items->map(fn ($product, $slug) => $typeFor($slug))->filter()->implode(' + '))
                                    <span class="rituals-pair-note">{{ $pairNote }}</span>
                                @endif
                            </div>
                        @endif
                    </div>
                    <div class="rituals-products">
                        @foreach($items as $slug => $product)
                            @php
                                $media = $product->media->first();
                                $hover = $product->media->get(1);
                                $image = $media ? ($media->hasGeneratedConversion('small') ? $media->getUrl('small') : $media->getUrl()) : null;
                                $hoverImage = $hover ? ($hover->hasGeneratedConversion('small') ? $hover->getUrl('small') : $hover->getUrl()) : null;
                                $url = localized_route('shop.item', ['url' => $product->url]);
                            @endphp
                            <article class="rituals-product" data-reveal style="--d: {{ number_format(.12 + $loop->index * .14, 2) }}s">
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
                                    <span class="rituals-product-more" aria-hidden="true"><x-tabler-arrow-up-right class="size-5" stroke-width="1.4" /></span>
                                </a>
                                <div class="rituals-product-body">
                                    @if($type = $typeFor($slug))
                                        <span class="rituals-product-type">{{ $type }}</span>
                                    @endif
                                    <h3 class="rituals-product-title"><a href="{{ $url }}">{{ $product->title }}</a></h3>
                                    <div class="rituals-product-meta">
                                        <span>{{ $product->volume > 1 ? $product->volume . ' ml' : '' }}</span>
                                        @if($product->price > 0)
                                            <span class="rituals-product-price">{{ $money($product->price) }}</span>
                                        @endif
                                    </div>
                                    @if($product->availability && !$product->preorder && $product->price > 0 && $product->sizes->count() <= 1)
                                        <button type="button" x-data="{ product: @js($cartProducts->get($product->id)) }" @click="$store.cart.addItem(product, null, product.sizes[0]?.id ?? null)" class="rituals-product-action">
                                            {{ __('Add to Cart') }}
                                            <x-tabler-shopping-bag class="size-5" stroke-width="1.3" />
                                        </button>
                                    @elseif(!$product->availability && !$product->preorder)
                                        <a href="{{ $url }}" class="rituals-out">{{ $fr ? 'Rupture de stock' : 'Out of stock' }}</a>
                                    @else
                                        <a href="{{ $url }}" class="rituals-product-action rituals-product-action--secondary">
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

        <section class="rituals-end" data-reveal>
            <p class="rituals-kicker">PLUSH Rituals</p>
            <h2>{{ $fr ? 'Un instant à soi.' : 'A moment for yourself.' }}</h2>
            <a href="#rituals" class="rituals-end-link">{{ $fr ? 'Retrouver mon rituel' : 'Find your ritual' }} <x-tabler-arrow-up class="size-4" stroke-width="1.4" /></a>
        </section>
        <script>
            (() => {
                const items = document.querySelectorAll('.rituals [data-reveal]');
                if (!('IntersectionObserver' in window)) { items.forEach(el => el.classList.add('is-visible')); return; }
                const io = new IntersectionObserver((entries) => entries.forEach(entry => {
                    if (!entry.isIntersecting) return;
                    entry.target.classList.add('is-visible');
                    io.unobserve(entry.target);
                }), { rootMargin: '0px 0px -8% 0px', threshold: .08 });
                items.forEach(el => io.observe(el));
            })();
        </script>
    </main>
</x-layout>
