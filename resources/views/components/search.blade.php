<div
    x-data="siteSearch({
        endpoint: @js(localized_route('api.search')),
    })"
    x-cloak
    @open-site-search.window="show()"
    @keydown.escape.window="hide()"
>
    <div
        x-show="opened"
        class="fixed inset-0 z-[9999]"
        role="dialog"
        aria-modal="true"
    >
        {{-- Backdrop --}}
        <button
            type="button"
            aria-label="{{ __('Close') }}"
            class="absolute inset-0 cursor-default bg-black/45 backdrop-blur-md"
            @click="hide()"
            x-show="opened"
            x-transition.opacity.duration.300ms
        ></button>

        {{-- Search panel --}}
        <section
            x-show="opened"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="-translate-y-6 opacity-0"
            x-transition:enter-end="translate-y-0 opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="translate-y-0 opacity-100"
            x-transition:leave-end="-translate-y-4 opacity-0"
            class="relative z-10 h-[100dvh] w-full overflow-y-auto overscroll-contain bg-white md:h-auto md:max-h-[94dvh] md:shadow-2xl"
        >
            <div class="container py-6 sm:py-8 lg:py-10">

                {{-- Close --}}
                <div class="mb-10 flex justify-end">
                    <button
                        type="button"
                        @click="hide()"
                        class="group flex items-center gap-2 text-sm font-medium text-neutral-600 transition hover:text-black"
                    >
                        <x-tabler-x
                            class="h-5 w-5 transition-transform duration-300 group-hover:rotate-90"
                            stroke-width="1.4"
                        />

                        {{ __('Close') }}
                    </button>
                </div>

                {{-- Input --}}
                <form
                    class="relative border-b border-neutral-200 pb-6 sm:pb-8"
                    @submit.prevent="goToFirstResult()"
                >
                    <input
                        x-ref="input"
                        x-model="query"
                        @input="queueSearch()"
                        type="search"
                        autocomplete="off"
                        spellcheck="false"
                        placeholder="{{ __('Search products') }}..."
                        class="w-full appearance-none bg-transparent pr-16 text-3xl font-medium tracking-[-0.04em] text-black placeholder:text-neutral-300 focus:outline-none sm:text-5xl"
                    >

                    <div class="pointer-events-none absolute bottom-7 right-0 flex h-10 w-10 items-center justify-center sm:bottom-9">
                        <x-tabler-search
                            x-show="!loading"
                            class="h-8 w-8 text-neutral-500 sm:h-10 sm:w-10"
                            stroke-width="1.3"
                        />

                        <x-tabler-loader-2
                            x-show="loading"
                            class="h-8 w-8 animate-spin text-black sm:h-10 sm:w-10"
                            stroke-width="1.3"
                        />
                    </div>
                </form>

                {{-- Initial state --}}
                <div
                    x-show="!hasSearched && !loading"
                    class="py-8 text-sm text-neutral-500 sm:text-base"
                >
                    {{ __('Start typing to search products and collections.') }}
                </div>

                {{-- Skeleton loader --}}
                <div
                    x-show="loading"
                    class="py-8 sm:py-10"
                >
                    <div class="grid grid-cols-2 gap-x-3 gap-y-8 md:grid-cols-3 lg:grid-cols-5 lg:gap-x-5">
                        <template x-for="item in [1, 2, 3, 4, 5]" :key="item">
                            <div class="animate-pulse">
                                <div class="aspect-[4/5] rounded-sm bg-neutral-100"></div>

                                <div class="mt-4 h-3 w-2/3 rounded-full bg-neutral-100"></div>
                                <div class="mt-2 h-2.5 w-1/3 rounded-full bg-neutral-100"></div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Error --}}
                <div
                    x-show="error && !loading"
                    class="py-16 text-center"
                >
                    <p class="text-lg font-medium text-black">
                        {{ __('Search failed. Please try again.') }}
                    </p>
                </div>

                {{-- Results --}}
                <div
                    x-show="hasSearched && !loading && !error && results.total > 0"
                    class="space-y-10 py-8 sm:py-10"
                    aria-live="polite"
                >
                    {{-- Collections --}}
                    <section x-show="results.collections.length > 0">
                        <div class="mb-5 flex items-center justify-between">
                            <h2 class="text-xs font-medium uppercase tracking-[0.16em] text-neutral-400">
                                {{ __('Collections') }}
                            </h2>
                        </div>

                        <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                            <template
                                x-for="collection in results.collections"
                                :key="`collection-${collection.id}`"
                            >
                                <a
                                    :href="collection.url"
                                    @click="hide()"
                                    class="group flex min-h-20 items-center justify-between rounded-2xl bg-neutral-50 px-5 py-4 transition duration-300 hover:bg-black hover:text-white"
                                >
                                    <div class="min-w-0">
                                        <p
                                            class="truncate text-base font-medium"
                                            x-text="collection.title"
                                        ></p>

                                        <p
                                            x-show="collection.description"
                                            class="mt-1 line-clamp-1 text-sm text-neutral-500 transition group-hover:text-neutral-300"
                                            x-text="collection.description"
                                        ></p>
                                    </div>

                                    <x-tabler-arrow-up-right
                                        class="ml-4 h-5 w-5 shrink-0 transition-transform duration-300 group-hover:-translate-y-0.5 group-hover:translate-x-0.5"
                                        stroke-width="1.4"
                                    />
                                </a>
                            </template>
                        </div>
                    </section>

                    {{-- Products --}}
                    <section x-show="results.products.length > 0">
                        <div class="mb-5 flex items-center justify-between">
                            <h2 class="text-xs font-medium uppercase tracking-[0.16em] text-neutral-400">
                                {{ __('Products') }}
                            </h2>
                        </div>

                        <div class="grid grid-cols-2 gap-x-3 gap-y-8 md:grid-cols-3 lg:grid-cols-5 lg:gap-x-5">
                            <template
                                x-for="product in results.products"
                                :key="`product-${product.id}`"
                            >
                                <a
                                    :href="product.url"
                                    @click="hide()"
                                    class="group block min-w-0"
                                >
                                    <div class="relative bg-neutral-100">
                                        <img
                                            x-show="product.image"
                                            :src="product.image"
                                            :alt="product.title"
                                            class="w-full object-cover transition-transform duration-700 ease-out group-hover:scale-[1.035]"
                                        >

                                        <div
                                            x-show="!product.image"
                                            class="flex h-full w-full items-center justify-center text-neutral-300"
                                        >
                                            <x-tabler-photo
                                                class="h-8 w-8"
                                                stroke-width="1.2"
                                            />
                                        </div>

                                        <div class="absolute inset-x-3 bottom-3 flex justify-end">
                                            <span class="flex h-10 w-10 translate-y-2 items-center justify-center rounded-full bg-black text-white opacity-0 shadow-lg transition duration-300 group-hover:translate-y-0 group-hover:opacity-100">
                                                <x-tabler-arrow-up-right
                                                    class="h-4 w-4"
                                                    stroke-width="1.4"
                                                />
                                            </span>
                                        </div>
                                    </div>

                                    <footer class="mt-3">
                                        <div class="flex items-start justify-between gap-3">
                                            <h3
                                                class="min-w-0 text-sm font-medium leading-snug text-black"
                                                x-text="product.title"
                                            ></h3>

                                            <p
                                                x-show="Number(product.price) > 0"
                                                class="shrink-0 text-sm text-neutral-500"
                                                x-text="formatPrice(product.price)"
                                            ></p>
                                        </div>

                                        <p
                                            x-show="product.description"
                                            class="mt-1 line-clamp-2 text-xs leading-5 text-neutral-400"
                                            x-text="product.description"
                                        ></p>

                                        <p class="mt-2 flex items-center gap-1.5 text-[10px] font-medium uppercase tracking-[0.14em] text-neutral-400 transition group-hover:text-black">
                                            <span class="h-1.5 w-1.5 rounded-full bg-neutral-300 transition group-hover:bg-black"></span>
                                            {{ __('View details') }}
                                        </p>
                                    </footer>
                                </a>
                            </template>
                        </div>
                    </section>
                </div>

                {{-- Empty state --}}
                <div
                    x-show="hasSearched && !loading && !error && results.total === 0"
                    class="py-20 text-center"
                >
                    <p class="text-xl font-medium text-black">
                        {{ __('No results found') }}
                    </p>

                    <p class="mt-2 text-sm text-neutral-500">
                        {{ __('Try a different word or phrase.') }}
                    </p>
                </div>
            </div>
        </section>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('siteSearch', ({ endpoint }) => ({
            endpoint,

            opened: false,
            query: '',
            loading: false,
            error: false,
            hasSearched: false,

            timer: null,
            controller: null,
            cache: {},

            results: {
                products: [],
                collections: [],
                total: 0,
            },

            show() {
                this.opened = true;

                document.documentElement.classList.add('overflow-hidden');
                document.body.classList.add('overflow-hidden');

                this.$nextTick(() => {
                    setTimeout(() => this.$refs.input?.focus(), 120);
                });
            },

            hide() {
                this.opened = false;

                document.documentElement.classList.remove('overflow-hidden');
                document.body.classList.remove('overflow-hidden');

                clearTimeout(this.timer);
                this.controller?.abort();
            },

            normalizedQuery() {
                return this.query
                    .replace(/\s+/g, ' ')
                    .trim()
                    .slice(0, 80);
            },

            queueSearch() {
                clearTimeout(this.timer);

                const query = this.normalizedQuery();

                if (query.length < 2) {
                    this.controller?.abort();

                    this.loading = false;
                    this.error = false;
                    this.hasSearched = false;

                    this.results = {
                        products: [],
                        collections: [],
                        total: 0,
                    };

                    return;
                }

                this.timer = setTimeout(() => {
                    this.searchNow();
                }, 320);
            },

            async searchNow() {
                const query = this.normalizedQuery();
                const cacheKey = query.toLocaleLowerCase();

                if (query.length < 2) {
                    return;
                }

                if (this.cache[cacheKey]) {
                    this.results = this.cache[cacheKey];
                    this.hasSearched = true;
                    this.loading = false;
                    this.error = false;

                    return;
                }

                this.controller?.abort();

                const controller = new AbortController();

                this.controller = controller;
                this.loading = true;
                this.error = false;
                this.hasSearched = true;

                try {
                    const url = new URL(this.endpoint, window.location.origin);

                    url.searchParams.set('q', query);

                    const response = await fetch(url.toString(), {
                        method: 'GET',
                        credentials: 'same-origin',
                        signal: controller.signal,
                        headers: {
                            Accept: 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });

                    if (!response.ok) {
                        throw new Error(`Search request failed: ${response.status}`);
                    }

                    const data = await response.json();

                    if (this.normalizedQuery().toLocaleLowerCase() !== cacheKey) {
                        return;
                    }

                    const results = {
                        products: Array.isArray(data.products) ? data.products : [],
                        collections: Array.isArray(data.collections) ? data.collections : [],
                        total: Number(data.total ?? 0),
                    };

                    this.cache[cacheKey] = results;
                    this.results = results;
                } catch (error) {
                    if (error.name !== 'AbortError') {
                        console.error(error);

                        this.error = true;
                        this.results = {
                            products: [],
                            collections: [],
                            total: 0,
                        };
                    }
                } finally {
                    if (this.controller === controller) {
                        this.loading = false;
                    }
                }
            },

            goToFirstResult() {
                const firstResult =
                    this.results.products[0]
                    ?? this.results.collections[0]
                    ?? null;

                if (firstResult?.url) {
                    window.location.href = firstResult.url;
                }
            },

            formatPrice(price) {
                return new Intl.NumberFormat(
                    document.documentElement.lang || 'en',
                    {
                        style: 'currency',
                        currency: 'EUR',
                        maximumFractionDigits: 0,
                    }
                ).format(Number(price));
            },

            destroy() {
                clearTimeout(this.timer);
                this.controller?.abort();

                document.documentElement.classList.remove('overflow-hidden');
                document.body.classList.remove('overflow-hidden');
            },
        }));
    });
</script>
