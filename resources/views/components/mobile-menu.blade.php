<div
    x-data="{ opened: false }"
    x-cloak
    @open-mobile-menu.window="
        opened = true;
        document.documentElement.classList.add('overflow-hidden');
        document.body.classList.add('overflow-hidden');
    "
    @keydown.escape.window="
        opened = false;
        document.documentElement.classList.remove('overflow-hidden');
        document.body.classList.remove('overflow-hidden');
    "
>
    <div
        x-show="opened"
        class="fixed inset-0 z-[99999] md:hidden"
        role="dialog"
        aria-modal="true"
    >
        {{-- Backdrop --}}
        <button
            type="button"
            x-show="opened"
            x-transition:enter="transition-opacity duration-400"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="
                opened = false;
                document.documentElement.classList.remove('overflow-hidden');
                document.body.classList.remove('overflow-hidden');
            "
            class="absolute inset-0 cursor-default bg-black/35 backdrop-blur-md"
            aria-label="{{ __('Close menu') }}"
        ></button>

        {{-- Drawer --}}
        <aside
            x-show="opened"
            x-transition:enter="transition-transform duration-500 ease-[cubic-bezier(.22,1,.36,1)]"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition-transform duration-300 ease-in"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="absolute inset-y-0 right-0 flex w-[92%] max-w-sm flex-col bg-[#f7f7f5] shadow-2xl"
        >
            <header class="flex items-center justify-between px-6 py-5">
                <a
                    href="{{ localized_route('home') }}"
                    class="text-lg font-medium tracking-[-0.03em]"
                >
                    Maison Plush
                </a>

                <button
                    type="button"
                    @click="
                        opened = false;
                        document.documentElement.classList.remove('overflow-hidden');
                        document.body.classList.remove('overflow-hidden');
                    "
                    class="group flex h-11 w-11 items-center justify-center rounded-full bg-white shadow-sm transition duration-300 hover:bg-black"
                    aria-label="{{ __('Close menu') }}"
                >
                    <x-tabler-x
                        class="h-5 w-5 text-black transition duration-300 group-hover:rotate-90 group-hover:text-white"
                        stroke-width="1.4"
                    />
                </button>
            </header>

            <nav class="flex-1 overflow-y-auto px-6 py-10">
                <p class="mb-7 text-[10px] font-medium uppercase tracking-[0.18em] text-neutral-400">
                    {{ __('Navigation') }}
                </p>

                @php
                    $menu = [
                         [
                            'label' => __('Home'),
                            'route' => localized_route('home'),
                        ],
                        [
                            'label' => __('Shop'),
                            'route' => localized_route('shop'),
                        ],
                        [
                            'label' => __('Collections'),
                            'route' => localized_route('collections'),
                        ],
                        [
                            'label' => __('About Us'),
                            'route' => localized_route('about'),
                        ],
                        [
                            'label' => __('Contact Us'),
                            'route' => localized_route('contact'),
                        ],
                    ];
                @endphp

                <div class="space-y-1">
                    @foreach($menu as $index => $item)
                        <a
                            href="{{ $item['route'] }}"
                            class="group flex items-center justify-between rounded-2xl px-4 py-4 transition duration-300 hover:bg-white"
                        >
                            <span class="flex items-center gap-4">
                                <span class="w-5 text-[10px] text-neutral-300">
                                    {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                                </span>

                                <span class="text-2xl font-medium tracking-[-0.04em] text-neutral-900">
                                    {{ $item['label'] }}
                                </span>
                            </span>

                            <x-tabler-arrow-up-right
                                class="h-5 w-5 translate-y-1 text-neutral-300 opacity-0 transition duration-300 group-hover:translate-y-0 group-hover:text-black group-hover:opacity-100"
                                stroke-width="1.4"
                            />
                        </a>
                    @endforeach
                </div>

                <div class="mt-12 grid grid-cols-2 gap-3">
                    <button
                        type="button"
                        @click="
                            opened = false;
                            document.documentElement.classList.remove('overflow-hidden');
                            document.body.classList.remove('overflow-hidden');

                            $nextTick(() => {
                                window.dispatchEvent(
                                    new CustomEvent('open-site-search')
                                );
                            });
                        "
                        class="flex items-center gap-3 rounded-2xl bg-white px-4 py-4 text-sm font-medium shadow-sm transition hover:bg-black hover:text-white"
                    >
                        <x-tabler-search class="h-5 w-5" stroke-width="1.4" />
                        {{ __('Search') }}
                    </button>

                    <a
                        href="{{ localized_route('wishlist') }}"
                        class="flex items-center gap-3 rounded-2xl bg-white px-4 py-4 text-sm font-medium shadow-sm transition hover:bg-black hover:text-white"
                    >
                        <x-tabler-heart class="h-5 w-5" stroke-width="1.4" />
                        {{ __('Wishlist') }}
                    </a>
                </div>

                {{-- Languages --}}
                <div class="mt-10">
                    <p class="mb-4 text-[10px] font-medium uppercase tracking-[0.18em] text-neutral-400">
                        {{ __('Language') }}
                    </p>

                    <div class="flex gap-2">
                        <a
                            href="/"
                            class="flex h-11 min-w-16 items-center justify-center rounded-full px-5 text-sm font-medium transition
                                {{ app()->getLocale() === 'en'
                                    ? 'bg-black text-white'
                                    : 'bg-white text-black hover:bg-neutral-200' }}"
                        >
                            EN
                        </a>

                        <a
                            href="/fr"
                            class="flex h-11 min-w-16 items-center justify-center rounded-full px-5 text-sm font-medium transition
                                {{ app()->getLocale() === 'fr'
                                    ? 'bg-black text-white'
                                    : 'bg-white text-black hover:bg-neutral-200' }}"
                        >
                            FR
                        </a>
                    </div>
                </div>
            </nav>
        </aside>
    </div>
</div>
