@props(['galleryMedia', 'imagesData'])

<div class="product-gallery">
    @if($galleryMedia->count() > 0)
        <div x-data="productGallery({{ $galleryMedia->count() }}, @js($imagesData))">
            <!-- Main image display -->
            <div class="relative mb-2 overflow-hidden bg-gray-100">
                <!-- Embla viewport -->
                <div
                    x-ref="viewport"
                    class="aspect-[3/4] overflow-hidden select-none"
                    style="touch-action: pan-y pinch-zoom;"
                    @click="handleClick"
                    @pointerdown="recordPointerDown($event)"
                    @pointerup.window="recordPointerUp($event)"
                    :class="isZoomed ? 'cursor-zoom-out' : 'cursor-zoom-in'"
                >
                    <!-- Embla container -->
                    <div class="flex h-full">
                        <template x-for="(image, index) in slides" :key="index">
                            <div class="relative h-full min-w-0 flex-[0_0_100%]">
                                <!-- Zoom view -->
                                <div
                                    x-show="isZoomed && activeSlide === (index % totalSlides)"
                                    class="absolute inset-0 h-full w-full"
                                    :style="{
                                        backgroundImage: 'url(' + image.large + ')',
                                        backgroundPosition: (zoomPosition.x * 100) + '% ' + (zoomPosition.y * 100) + '%',
                                        backgroundSize: '150%',
                                        backgroundRepeat: 'no-repeat'
                                    }"
                                    @mousemove="handleMouseMove($event)"
                                ></div>

                                <!-- Normal view -->
                                <picture x-show="!isZoomed" class="block h-full w-full">
                                    <source :srcset="image.largea" type="image/avif">
                                    <source :srcset="image.large" type="image/webp">
                                    <img
                                        :src="image.large"
                                        :alt="image.alt + ' - {{ __('img') }} ' + ((index % totalSlides) + 1)"
                                        class="h-full w-full object-cover object-center"
                                        draggable="false"
                                    >
                                </picture>
                            </div>
                        </template>
                    </div>

                    <!-- Zoom instruction -->
                    <div
                        class="pointer-events-none absolute inset-0 flex items-center justify-center opacity-0 transition-opacity duration-300 hover:opacity-100"
                        :class="{ 'hidden': isZoomed || isDragging }"
                    >
                        <div class="flex items-center rounded-sm bg-white/80 px-3 py-1.5 text-xs text-zinc-800 shadow-sm backdrop-blur-sm">
                            <x-tabler-zoom-in class="mr-1.5 h-3.5 w-3.5" stroke-width="1.5" />
                            <span>{{ __('Click to zoom') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Navigation arrows -->
                <button
                    type="button"
                    @click.stop="goPrev()"
                    class="absolute left-3 top-1/2 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-full bg-white/90 text-zinc-800 shadow-sm backdrop-blur-sm transition-colors duration-300 hover:bg-white"
                    :class="{ 'hidden': isZoomed || totalSlides <= 1 }"
                    aria-label="{{ __('Previous image') }}"
                >
                    <x-tabler-chevron-left class="h-4 w-4" stroke-width="1.5" />
                </button>

                <button
                    type="button"
                    @click.stop="goNext()"
                    class="absolute right-3 top-1/2 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-full bg-white/90 text-zinc-800 shadow-sm backdrop-blur-sm transition-colors duration-300 hover:bg-white"
                    :class="{ 'hidden': isZoomed || totalSlides <= 1 }"
                    aria-label="{{ __('Next image') }}"
                >
                    <x-tabler-chevron-right class="h-4 w-4" stroke-width="1.5" />
                </button>

                <!-- Image counter -->
                <div
                    class="absolute bottom-3 right-3 rounded-sm bg-white/90 px-2.5 py-1 text-xs text-zinc-700 shadow-sm backdrop-blur-sm"
                    :class="{ 'hidden': isZoomed || totalSlides <= 1 }"
                >
                    <span x-text="activeSlide + 1"></span> / <span x-text="totalSlides"></span>
                </div>
            </div>

            @if($galleryMedia->count() > 1)
                <div class="hidden sm:block">
                    <div
                        x-ref="thumbScroller"
                        class="thumb-scroller flex gap-2 overflow-x-auto scroll-smooth"
                    >
                        <template x-for="(image, index) in images" :key="index">
                            <button
                                type="button"
                                @click="goTo(index)"
                                :data-thumb-index="index"
                                class="aspect-square shrink-0 overflow-hidden border"
                                style="width: calc((100% - 3 * 0.5rem) / 4);"
                                :class="activeSlide === index
                                    ? 'border-zinc-800'
                                    : 'border-gray-200 opacity-70 hover:opacity-100 transition-opacity duration-300'"
                            >
                                <picture class="block h-full w-full">
                                    <source :srcset="image.thumb" type="image/webp">
                                    <img
                                        :src="image.thumb"
                                        :alt="'{{ __('Thumbnail') }} ' + (index + 1)"
                                        class="h-full w-full object-cover object-center"
                                        draggable="false"
                                    >
                                </picture>
                            </button>
                        </template>
                    </div>
                </div>
            @endif
        </div>
    @else
        <div class="flex aspect-[3/4] items-center justify-center bg-zinc-100">
            <x-tabler-photo class="h-16 w-16 text-zinc-400" stroke-width="1.5" />
        </div>
    @endif
</div>

<style>
    .thumb-scroller {
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    .thumb-scroller::-webkit-scrollbar {
        display: none;
    }
</style>
