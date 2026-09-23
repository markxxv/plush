@props(['galleryMedia', 'imagesData'])
<div class="product-gallery">
    @if($galleryMedia->count() > 0)
        <div x-data="productGallery({{ $galleryMedia->count() }}, @js($imagesData))">
            <!-- Main image display -->
            <div class="relative mb-2 overflow-hidden bg-gray-100">
                <!-- Large image container -->
                <div
                    class="aspect-[3/4] overflow-hidden select-none"
                    style="touch-action: pan-y;"
                    @click="handleClick"
                    @pointerdown="onDown"
                    @pointermove.window="onMove"
                    @pointerup.window="onUp"
                    @pointercancel.window="onUp"
                    :class="isZoomed ? 'cursor-zoom-out' : 'cursor-zoom-in'"
                >
                    <!-- Main images -->
                    <div
                        class="relative w-full h-full"
                        :style="`transform: translate3d(${currentX()}px, 0, 0); transition: ${isDragging || animating ? 'none' : 'transform 350ms cubic-bezier(0.33, 1, 0.68, 1)'}; will-change: transform;`"
                    >
                        <template x-for="(image, index) in images" :key="index">
                            <div
                                class="absolute inset-0 w-full h-full"
                                :style="`left: ${index * 100}%;`"
                            >
                                <!-- Zoom view -->
                                <div
                                    x-show="isZoomed && activeSlide === index"
                                    class="absolute inset-0 w-full h-full"
                                    :style="`background-image: url('${image.large}'); background-position: ${zoomPosition.x * 100}% ${zoomPosition.y * 100}%; background-size: 150%; background-repeat: no-repeat;`"
                                    @mousemove="handleMouseMove($event)"
                                ></div>

                                <!-- Normal view -->
                                <picture x-show="!isZoomed">
                                    <source :srcset="image.large_avif" type="image/avif">
                                    <source :srcset="image.large" type="image/webp">
                                    <img
                                        :src="image.large"
                                        :alt="image.alt + ' - {{ __('img') }} ' + (index + 1)"
                                        class="w-full h-full object-cover object-center"
                                        draggable="false"
                                    >
                                </picture>
                            </div>
                        </template>
                    </div>

                    <!-- Zoom instruction -->
                    <div
                        class="absolute inset-0 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity duration-300 pointer-events-none"
                        :class="{ 'hidden': isZoomed || isDragging }"
                    >
                        <div class="bg-white/80 backdrop-blur-sm text-zinc-800 text-xs px-3 py-1.5 rounded-sm shadow-sm flex items-center">
                            <x-tabler-zoom-in class="w-3.5 h-3.5 mr-1.5" stroke-width="1.5" />
                            <span>{{ __('Click to zoom') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Navigation arrows -->
                <button
                    @click="goTo(activeSlide - 1)"
                    class="absolute left-3 top-1/2 transform -translate-y-1/2 bg-white/90 backdrop-blur-sm text-zinc-800 hover:bg-white transition-colors duration-300 w-8 h-8 flex items-center justify-center rounded-full shadow-sm"
                    :class="{ 'hidden': isZoomed || totalSlides <= 1 }"
                >
                    <x-tabler-chevron-left class="w-4 h-4" stroke-width="1.5" />
                </button>

                <button
                    @click="goTo(activeSlide + 1)"
                    class="absolute right-3 top-1/2 transform -translate-y-1/2 bg-white/90 backdrop-blur-sm text-zinc-800 hover:bg-white transition-colors duration-300 w-8 h-8 flex items-center justify-center rounded-full shadow-sm"
                    :class="{ 'hidden': isZoomed || totalSlides <= 1 }"
                >
                    <x-tabler-chevron-right class="w-4 h-4" stroke-width="1.5" />
                </button>

                <!-- Image counter -->
                <div
                    class="absolute bottom-3 right-3 bg-white/90 backdrop-blur-sm text-zinc-700 text-xs px-2.5 py-1 rounded-sm shadow-sm"
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
                            @click="goTo(index)"
                            :data-thumb-index="index"
                            class="aspect-square overflow-hidden border shrink-0"
                            style="width: calc((100% - 3 * 0.5rem) / 4);"
                            :class="activeSlide === index ? 'border-zinc-800' : 'border-gray-200 opacity-70 hover:opacity-100 transition-opacity duration-300'"
                        >
                            <picture>
                                <source :srcset="image.thumb_avif" type="image/avif">
                                <source :srcset="image.thumb" type="image/webp">
                                <img
                                    :src="image.thumb"
                                    :alt="'{{ __('Thumbnail') }} ' + (index + 1)"
                                    class="w-full h-full object-cover object-center"
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
        <div class="aspect-[3/4] bg-zinc-100 flex items-center justify-center">
            <x-tabler-photo class="w-16 h-16 text-zinc-400" stroke-width="1.5" />
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

<script>
    function productGallery(totalSlides, images) {
        return {
            // Config
            cfg: {
                duration: 350,
                threshold: 0.15,
                throwK: 220,
                maxThrowRatio: 0.6,
                edgeResistance: 0.35
            },

            // State
            activeSlide: 0,
            totalSlides: totalSlides,
            images: images,
            isZoomed: false,
            zoomPosition: { x: 0.5, y: 0.5 },

            // Swipe state
            isDragging: false,
            startX: 0,
            dragOffset: 0,
            baseX: 0,
            slideW: 0,
            lastX: 0,
            lastT: 0,
            velocity: 0,
            animating: false,
            animId: null,

            init() {
                this.recalc();
                window.addEventListener('resize', () => this.recalc());
            },

            recalc() {
                this.slideW = this.$el.querySelector('.aspect-\\[3\\/4\\]')?.offsetWidth || 0;
                this.baseX = -this.activeSlide * this.slideW;
            },

            currentX() {
                if (this.isDragging) {
                    const atStart = this.activeSlide === 0 && this.dragOffset > 0;
                    const atEnd = this.activeSlide === this.totalSlides - 1 && this.dragOffset < 0;
                    const edge = (atStart || atEnd) ? this.cfg.edgeResistance : 1;
                    return this.baseX + this.dragOffset * edge;
                }
                return this.baseX;
            },

            handleClick(e) {
                // Only toggle zoom if not dragging and not on mobile
                if (Math.abs(this.dragOffset) < 5 && !this.isDragging) {
                    this.isZoomed = !this.isZoomed;
                }
            },

            onDown(e) {
                if (this.isZoomed) return;
                if (e.pointerType === 'mouse' && e.button !== 0) return;

                this.cancelAnim();
                this.isDragging = true;
                this.startX = e.clientX;
                this.dragOffset = 0;
                this.baseX = -this.activeSlide * this.slideW;
                this.lastX = e.clientX;
                this.lastT = performance.now();
                this.velocity = 0;

                e.preventDefault();
                e.target.setPointerCapture?.(e.pointerId);
            },

            onMove(e) {
                if (!this.isDragging || this.isZoomed) return;

                const x = e.clientX;
                this.dragOffset = x - this.startX;

                const now = performance.now();
                const dt = now - this.lastT;
                if (dt > 0) {
                    const dx = x - this.lastX;
                    const v = dx / dt;
                    this.velocity = this.velocity * 0.8 + v * 0.2;
                    this.lastX = x;
                    this.lastT = now;
                }
            },

            onUp() {
                if (!this.isDragging) return;
                this.isDragging = false;

                const MAX_THROW = this.slideW * this.cfg.maxThrowRatio;
                let throwDist = this.velocity * this.cfg.throwK;
                throwDist = Math.max(-MAX_THROW, Math.min(MAX_THROW, throwDist));

                const target = this.baseX + this.dragOffset + throwDist;
                let targetIndex = Math.round(-target / this.slideW);

                const dragDist = this.dragOffset + throwDist;
                if (Math.abs(dragDist) > this.slideW * this.cfg.threshold) {
                    targetIndex = dragDist > 0 ? this.activeSlide - 1 : this.activeSlide + 1;
                }

                this.goTo(targetIndex, true);
            },

            goTo(index, animate = true) {
                const clamped = Math.max(0, Math.min(this.totalSlides - 1, index));
                const from = this.currentX();
                this.activeSlide = clamped;
                this.snapToIndex(animate, from);
                this.scrollThumbnailIntoView();
            },

            snapToIndex(animate, fromX = null) {
                const from = fromX ?? this.currentX();
                const to = -this.activeSlide * this.slideW;

                if (!animate) {
                    this.cancelAnim();
                    this.baseX = to;
                    this.dragOffset = 0;
                    this.velocity = 0;
                    return;
                }

                this.animateTo(from, to, this.cfg.duration);
            },

            animateTo(from, to, duration) {
                this.cancelAnim();
                this.animating = true;
                const start = performance.now();
                const easeOutCubic = t => 1 - Math.pow(1 - t, 3);

                const tick = (now) => {
                    const t = Math.min(1, (now - start) / duration);
                    this.baseX = from + (to - from) * easeOutCubic(t);
                    this.dragOffset = 0;

                    if (t < 1) {
                        this.animId = requestAnimationFrame(tick);
                    } else {
                        this.animId = null;
                        this.baseX = to;
                        this.animating = false;
                    }
                };

                this.animId = requestAnimationFrame(tick);
            },

            cancelAnim() {
                if (this.animId) {
                    cancelAnimationFrame(this.animId);
                    this.animId = null;
                    this.animating = false;
                }
            },

            scrollThumbnailIntoView() {
                this.$nextTick(() => {
                    const container = this.$refs.thumbScroller;
                    if (!container) return;

                    const activeBtn = container.querySelector(`[data-thumb-index="${this.activeSlide}"]`);
                    if (!activeBtn) return;

                    const containerRect = container.getBoundingClientRect();
                    const btnRect = activeBtn.getBoundingClientRect();

                    // Only scroll if the active thumbnail isn't fully visible
                    if (btnRect.left < containerRect.left || btnRect.right > containerRect.right) {
                        activeBtn.scrollIntoView({
                            behavior: 'smooth',
                            block: 'nearest',
                            inline: 'nearest'
                        });
                    }
                });
            },

            handleMouseMove(e) {
                if (!this.isZoomed) return;
                const bounds = e.target.getBoundingClientRect();
                const x = (e.clientX - bounds.left) / bounds.width;
                const y = (e.clientY - bounds.top) / bounds.height;
                this.zoomPosition = { x, y };
            }
        }
    }
</script>
