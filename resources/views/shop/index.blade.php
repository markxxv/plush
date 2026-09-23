@php
    $locale = app()->getLocale();

    $staticMeta = [
        'en' => [
            'title' => 'Shop Online - Maison Plush Paris',
            'description' => '',
        ],
        'fr' => [
            'title' => 'Boutique en ligne - Maison Plush Paris',
            'description' => '',
        ],
    ];

    $metaTitle = $staticMeta[$locale]['title'] ?? $staticMeta['en']['title'];
    $metaDescription = $staticMeta[$locale]['description'] ?? '';

    if (isset($collection)) {
        $metaTitle = $collection->{'meta_title_' . $locale}
            ?: $collection->{'name_' . $locale}
            ?: $collection->name_en
            ?: $metaTitle;

        $metaDescription = $collection->{'meta_description_' . $locale}
            ?: $metaDescription;
    }

    $currentCollection = $currentCollection ?? $collection ?? null;
    $collections = $collections ?? \App\Models\ProductCollection::where('active', true)->orderBy('position')->get();
@endphp

@section('metaTitle', $metaTitle)
@section('metaDescription', $metaDescription)

<x-layout>
    <header class="bg-white pt-21">
        <div class="bg-black py-6 text-white">
            <div class="container">
                <p class="pre_title mb-1 text-xs">LIMITED EDITION</p>

                <div class="flex flex-wrap items-baseline gap-x-3 gap-y-1">
                    <h1 class="section_title !text-3xl">
                        {{ app()->getLocale() === 'fr' ? 'Boutique' : 'Shop' }}
                    </h1>

                    @if($currentCollection)
                        <span class="text-xl font-light text-white/55">
                        / {{ $currentCollection->title }}
                    </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="container">
            <div class="relative">
                <nav class="flex gap-2 overflow-x-auto py-5 pr-16 scrollbar-none">
                    <a
                        href="{{ localized_route('shop') }}"
                        class="shrink-0 rounded-full px-5 py-2 text-sm transition
                        {{ $currentCollection
                            ? 'bg-neutral-100 text-neutral-500 hover:bg-neutral-200 hover:text-black'
                            : 'bg-black text-white' }}"
                    >
                        {{ app()->getLocale() === 'fr' ? 'Toutes' : 'All' }}
                    </a>

                    @foreach($collections as $collection)
                        @php
                            $slug = $collection->{'slug_' . app()->getLocale()}
                                ?: $collection->slug_en;

                            $active = $currentCollection?->id === $collection->id;
                        @endphp

                        <a
                            href="{{ localized_route('shop', ['collection' => $slug]) }}"
                            class="shrink-0 rounded-full px-5 py-2 text-sm transition
                            {{ $active
                                ? 'bg-black text-white'
                                : 'bg-neutral-100 text-neutral-500 hover:bg-neutral-200 hover:text-black' }}"
                        >
                            {{ $collection->title }}
                        </a>
                    @endforeach
                </nav>

                <div class="pointer-events-none absolute inset-y-0 right-0 w-20 bg-gradient-to-l from-white via-white/90 to-transparent"></div>
            </div>
        </div>
    </header>

    <section class="section">
        <div class="container">
            <div class="products_grid4 mt-10">
                @foreach ($products as $product)
                    @include('shop._card')
                @endforeach
            </div>
            <div class="mt-12">
                {{ $products->links() }}
            </div>
        </div>
    </section>


    @if(isset($currentCollection) && $currentCollection->description)
        <section class="container my-12 lg:my-24">
            <div class="grid md:grid-cols-2 gap-12 lg:gap-24 items-center">

                @if($currentCollection->video)
                    <!-- Custom Video Player (always horizontal) -->
                    <div
                        x-data="{
                    playing: false,
                    muted: false,
                    progress: 0,
                    showControls: true,
                    hideTimer: null,
                    togglePlay() {
                        this.playing ? this.$refs.vid.pause() : this.$refs.vid.play();
                    },
                    toggleMute() {
                        this.muted = !this.muted;
                        this.$refs.vid.muted = this.muted;
                    },
                    onTimeUpdate() {
                        this.progress = (this.$refs.vid.currentTime / this.$refs.vid.duration) * 100 || 0;
                    },
                    seek(e) {
                        const rect = this.$refs.bar.getBoundingClientRect();
                        const pct = (e.clientX - rect.left) / rect.width;
                        this.$refs.vid.currentTime = pct * this.$refs.vid.duration;
                    },
                    wake() {
                        this.showControls = true;
                        clearTimeout(this.hideTimer);
                        this.hideTimer = setTimeout(() => { if (this.playing) this.showControls = false }, 2200);
                    }
                }"
                        @mousemove="wake()"
                        class="relative aspect-video w-full overflow-hidden rounded-3xl bg-black group"
                    >
                        <video
                            x-ref="vid"
                            src="/storage/{{ $currentCollection->video }}"
                            @if($currentCollection->cover) poster="{{ $currentCollection->cover }}" @endif
                            class="absolute inset-0 w-full h-full object-cover"
                            playsinline
                            loop
                            @click="togglePlay()"
                            @play="playing = true"
                            @pause="playing = false"
                            @timeupdate="onTimeUpdate()"
                        ></video>

                        <!-- Center Play Button -->
                        <button
                            @click="togglePlay()"
                            x-show="!playing"
                            x-transition.opacity
                            class="absolute inset-0 m-auto w-20 h-20 rounded-full bg-white/90 hover:bg-white flex items-center justify-center transition-colors"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M7 4v16l13 -8z" fill="black" stroke="none"/>
                            </svg>
                        </button>

                        <!-- Bottom Gradient -->
                        <div
                            class="absolute inset-x-0 bottom-0 h-32 bg-gradient-to-t from-black/70 to-transparent pointer-events-none transition-opacity duration-300"
                            :class="showControls || !playing ? 'opacity-100' : 'opacity-0'"
                        ></div>

                        <!-- Controls Bar -->
                        <div
                            x-show="showControls || !playing"
                            x-transition.opacity
                            class="absolute bottom-0 inset-x-0 p-6 flex items-center gap-4 text-white"
                        >
                            <button @click="togglePlay()" class="shrink-0">
                                <svg x-show="!playing" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="white" stroke="none"><path d="M7 4v16l13 -8z"/></svg>
                                <svg x-show="playing" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="6" y="4" width="4" height="16" rx="1" fill="white" stroke="none"/><rect x="14" y="4" width="4" height="16" rx="1" fill="white" stroke="none"/></svg>
                            </button>

                            <div
                                x-ref="bar"
                                @click="seek($event)"
                                class="relative flex-1 h-[2px] bg-white/30 rounded-full cursor-pointer"
                            >
                                <div class="absolute top-0 left-0 h-full bg-white rounded-full" :style="`width: ${progress}%`"></div>
                                <div
                                    class="absolute top-1/2 -translate-y-1/2 w-3 h-3 bg-white rounded-full transition-transform group-hover:scale-100 scale-0"
                                    :style="`left: calc(${progress}% - 6px)`"
                                ></div>
                            </div>

                            <button @click="toggleMute()" class="shrink-0">
                                <svg x-show="!muted" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M15 8a5 5 0 0 1 0 8" /><path d="M17.7 5a9 9 0 0 1 0 14" /><path d="M6 15h-2a1 1 0 0 1 -1 -1v-4a1 1 0 0 1 1 -1h2l3.5 -4.5a.8 .8 0 0 1 1.5 .5v14a.8 .8 0 0 1 -1.5 .5l-3.5 -4.5" />
                                </svg>
                                <svg x-show="muted" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M6 15h-2a1 1 0 0 1 -1 -1v-4a1 1 0 0 1 1 -1h2l3.5 -4.5a.8 .8 0 0 1 1.5 .5v14a.8 .8 0 0 1 -1.5 .5l-3.5 -4.5" /><path d="M16 10l4 4m0 -4l-4 4" />
                                </svg>
                            </button>
                        </div>
                    </div>
                @elseif($currentCollection->cover)
                    <!-- Cover Image (orientation-agnostic) -->
                    <div class="rounded-3xl overflow-hidden bg-neutral-100 flex items-center justify-center">
                        <img src="{{ $currentCollection->cover }}" alt="{{ $currentCollection->name }}" class="w-full h-auto max-h-[720px] object-contain">
                    </div>
                @endif

                <!-- Collection Story -->
                <div class="space-y-8">
                    <div>
                        <p class="pre_title mb-4">{{ $currentCollection->name }}</p>
                        <h2 class="section_title mb-6">{{ __('The Story') }}</h2>
                    </div>
                    <p class="text-neutral-500 leading-relaxed">
                        {!! $currentCollection->description !!}
                    </p>
                </div>
            </div>
        </section>
    @endif

</x-layout>
