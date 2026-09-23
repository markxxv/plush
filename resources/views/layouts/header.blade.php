<x-cart />

<header class="site_header">
    <div class="container font-normal">
        <fieldset class="left gap-4 items-center flex">
            <button
                type="button"
                onclick="window.dispatchEvent(new CustomEvent('open-site-search'))"
                class="inline-flex items-center justify-center"
                aria-label="{{ __('Search') }}"
            >
                <x-tabler-search class="h-4 w-4" stroke-width="1.2" />
            </button>
            <a x-data href="{{ localized_route('wishlist') }}" class="flex items-center gap-2">
                <x-tabler-heart class="w-4 h-4"  stroke-width="1.2"  />
                <div x-show="$store.wishlist.items.length > 0" x-text="$store.wishlist.items.length" class="text-sm">0</div>
            </a>
            <a href="{{ localized_route('home') }}" class="hidden md:inline-flex">{{ __('Home') }}</a>
        </fieldset>
        <fieldset class="site_header_center">
            <a href="{{ localized_route('shop') }}" class="hidden text-xs uppercase md:inline-block" style="letter-spacing: 1px;">{{ __('Shop') }}</a>
            <a href="{{ localized_route('home') }}"><img src="/img/logo.svg" width="150" alt="Maison Plush Paris"></a>
            <a href="{{ localized_route('collections') }}" class="hidden text-xs uppercase md:inline-block" style="letter-spacing: 1px;">{{ __('Collections') }}</a>
        </fieldset>
        <fieldset x-data class="site_header_right items-center">
{{--            <a href=""><svg  xmlns="http://www.w3.org/2000/svg"  width="18"  height="18"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="1"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-user"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /></svg></a>--}}
            <nav class="hidden md:flex space-x-6">
                <a href="/">English</a>
                <a href="/fr">Français</a>
            </nav>
{{--            <div x-data="{ open: false }">--}}
{{--                <button @click="open = true">--}}
{{--                    <x-tabler-shopping-bag  width="18" height="18"  stroke-width="1" />--}}
{{--                </button>--}}
{{--            </div>--}}

            <button  @click="$store.cart.toggleCart()" class="group relative p-1.5 rounded-full transition-colors duration-300 cursor-pointer">
                    <svg class="w-4 h-4 transition-colors duration-300"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"/>
                    </svg>
                <div x-show="$store.cart.count > 0" x-text="$store.cart.count" class="cart_counter absolute -top-0.5 -right-0.5 w-3 h-3 rounded-full text-[10px] flex items-center justify-center font-medium">0</div>
            </button>

            <button
                type="button"
                onclick="window.dispatchEvent(new CustomEvent('open-mobile-menu'))"
                class="group flex h-11 w-11 items-center justify-center rounded-full bg-white shadow-sm ring-1 ring-black/5 transition duration-300 hover:bg-black md:hidden"
                aria-label="{{ __('Open menu') }}"
            >
                <span class="flex w-5 flex-col gap-[5px]">
                    <span class="h-px w-full bg-black transition duration-300 group-hover:bg-white"></span>
                    <span class="h-px w-3/4 self-end bg-black transition duration-300 group-hover:bg-white"></span>
                </span>
            </button>
        </fieldset>
    </div>
</header>
