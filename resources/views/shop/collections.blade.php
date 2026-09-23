@section('metaTitle', 'Collections - Maison Plush Paris')
@section('metaDescription', '')

<x-layout>
    <header class="bg-white pt-21">
        <div class="bg-black text-white py-6">
            <div class="container">
                <p class="pre_title text-xs mb-1 uppercase">{{ __('Archive') }}</p>
                <h2 class="section_title !text-3xl">{{ __('Collections') }}</h2>
            </div>
        </div>
    </header>

    <section class="section my-12 lg:my-24">
        <div class="container space-y-24 lg:space-y-32">
            @foreach ($collections as $collection)
                <div>
                    <!-- Collection Header -->
                    <div class="flex items-end justify-between border-b border-zinc-200 pb-6 mb-10">
                        <div>
{{--                            <p class="pre_title mb-2">{{ $collection->subtitle ?? 'Collection' }}</p>--}}
                            <h2 class="section_title">{{ $collection->title }}</h2>
                        </div>
                        <a href="{{ localized_route('collection', ['url' => $collection->url]) }}" class="hidden md:flex items-center gap-2 text-sm hover:gap-3 transition-all">
                            {{ __('View Collection') }}
                            <x-tabler-arrow-right class="w-4 h-4" stroke-width="1.3" />
                        </a>
                    </div>

                    <!-- Products -->
                    <div class="products_grid4">
                        @foreach ($collection->products->take(4) as $product)
                            @include('shop._card')
                        @endforeach
                    </div>

                    <!-- Mobile View More -->
                    <div class="mt-10 text-center md:hidden">
                        <a href="{{ localized_route('collection', ['url' => $collection->url]) }}"class="btn inline-flex items-center gap-2">
                            {{ __('View Collection') }}
                            <x-tabler-arrow-right class="w-4 h-4" stroke-width="1.3" />
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
</x-layout>
