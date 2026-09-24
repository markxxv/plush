@section('metaTitle', 'Maison Plush Paris - Water Inspired Fashion')
@section('metaDescription', '')
@section('body_class', 'white_header')
@section('head')
    <link rel="preload" as="image" href="/img/poster.webp" fetchpriority="high">
@endsection

<x-layout>
    <section class="main_header video-section">
      <video
          class="video-bg"
          muted
          loop
          playsinline
          preload="none"
          poster="/img/poster.webp"
          data-hero-video
      >
          <source data-src="/img/main_products.mp4" type="video/mp4">
      </video>
      <div class="inner">
        <p class="pre_title">{{ __('LIMITED EDITION') }}</p>
        <h1>Luna Code 26</h1>
          <p class="mt-12">
              <a class="inline-block text-white/80 hover-text-white border-b border-white/30 hover:border-white/60 pb-1 duration-300" href="#collection">{{ __('Discover') }}</a>
          </p>
      </div>
    </section>

    <section id="collection" class="section my-12 lg:my-24">
      <div class="container">

        <div class="products_grid4 mt-10">
          @foreach ($topProducts as $product)
            @include('shop._card')
          @endforeach
        </div>

        <div class="pre_title text-xs mb-2 mt-12 uppercase">Limited Edition</div>
        <h2 class="section_title">Luna Code 26</h2>

        <div class="products_grid mt-10">
          @foreach ($topFashop as $fashion)
            @include('shop._card', ['product' => $fashion])
          @endforeach
        </div>

      </div>
    </section>


    <section class="container my-12 lg:my-24">
        <x-video
            src="/video/luna_code26.mp4"
            poster="/video/luna_thumb.webp"
        />
    </section>



    <section class="section my-12 lg:my-24">
        <div class="container">
            <p class="section_title">Aqua Vitalis New Collections<br> <span class="text-neutral-400">80% Water, 20% Style</span></p>
            <div class="products_grid mt-10">
                @foreach ($collection as $item)
                    @include('shop._card', ['product' => $item])
                @endforeach
            </div>

        </div>
    </section>



{{--    <section class="container my-12 lg:my-24 relative overflow-hidden">--}}
{{--  <div class="bg-black text-white rounded-3xl p-8 lg:p-16 relative">--}}
{{--      <div class="max-w-2xl relative z-10">--}}
{{--          <p class="pre_title mb-4">Join Our Journey</p>--}}
{{--          <h2 class="section_title mb-6">Dive into Exclusive Updates</h2>--}}
{{--          <p class="mb-8 text-neutral-400">Be the first to discover new collections, exclusive events, and the intersection of water and fashion.</p>--}}
{{--          <form class="flex gap-4 flex-col md:flex-row">--}}
{{--              <input type="email" placeholder="Your email address" class="bg-white/10 rounded-full px-6 py-3 flex-1 text-white placeholder:text-neutral-400">--}}
{{--              <button class="bg-white text-black rounded-full px-8 py-3 font-medium hover:bg-neutral-200 transition">Subscribe</button>--}}
{{--          </form>--}}
{{--      </div>--}}
{{--      <div class="absolute right-0 top-0 bottom-0 w-1/2 opacity-20">--}}
{{--          <video class="object-cover h-full w-full" autoplay loop muted playsinline>--}}
{{--              <source src="/img/pluch_26_loop.mp4" type="video/mp4">--}}
{{--          </video>--}}
{{--      </div>--}}
{{--  </div>--}}
{{--</section>--}}



{{--<!-- Why Choose Us Section -->--}}
{{--<section class="container my-12 lg:my-24">--}}
{{--  <div class="grid md:grid-cols-3 gap-8">--}}
{{--      <article class="text-center px-6">--}}
{{--          <div class="w-16 h-16 rounded-full bg-black flex items-center justify-center mx-auto mb-6">--}}
{{--              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">--}}
{{--                  <path d="M3 7v4a1 1 0 0 0 1 1h3" />--}}
{{--                  <path d="M7 7v10" />--}}
{{--                  <path d="M10 8v8a1 1 0 0 0 1 1h2a1 1 0 0 0 1 -1v-8a1 1 0 0 0 -1 -1h-2a1 1 0 0 0 -1 1z" />--}}
{{--                  <path d="M17 7v4a1 1 0 0 0 1 1h3" />--}}
{{--                  <path d="M21 7v10" />--}}
{{--              </svg>--}}
{{--          </div>--}}
{{--          <h3 class="font-medium text-lg mb-4">Sustainable Luxury</h3>--}}
{{--          <p class="text-neutral-500">Our commitment to water conservation reflects in every piece we create, blending luxury with environmental consciousness.</p>--}}
{{--      </article>--}}

{{--      <article class="text-center px-6">--}}
{{--          <div class="w-16 h-16 rounded-full bg-black flex items-center justify-center mx-auto mb-6">--}}
{{--              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">--}}
{{--                  <path d="M12 12c2-2.96 0-7-1-8c0 3.038-1.773 4.741-3 6c-1.226 1.26-2 3.24-2 5a6 6 0 1 0 12 0c0-1.532-1.056-3.94-2-5c-1.786 3-2.791 3-4 2z" />--}}
{{--              </svg>--}}
{{--          </div>--}}
{{--          <h3 class="font-medium text-lg mb-4">Water Innovation</h3>--}}
{{--          <p class="text-neutral-500">Advanced water-based technologies and treatments ensure our fabrics maintain both comfort and style.</p>--}}
{{--      </article>--}}

{{--      <article class="text-center px-6">--}}
{{--          <div class="w-16 h-16 rounded-full bg-black flex items-center justify-center mx-auto mb-6">--}}
{{--              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">--}}
{{--                  <path d="M12 3c.132 0 .263 0 .393 0a7.5 7.5 0 0 0 7.92 12.446a9 9 0 1 1 -8.313 -12.454z" />--}}
{{--              </svg>--}}
{{--          </div>--}}
{{--          <h3 class="font-medium text-lg mb-4">Timeless Design</h3>--}}
{{--          <p class="text-neutral-500">Each piece is crafted to transcend seasons, combining classic elegance with modern innovation.</p>--}}
{{--      </article>--}}
{{--  </div>--}}
{{--</section>--}}

</x-layout>
