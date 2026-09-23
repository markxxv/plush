@props(['product'])
<div x-data="{ open: false }" @keydown.escape.window="open = false">

    <button
        type="button"
        @click="open = true"
        class="w-full bg-black text-white rounded-xl py-4 hover:bg-neutral-800 transition"
    >
        {{ __('Pre-order') }}
    </button>

    <div
        x-show="open"
        x-cloak
        x-transition.opacity
        class="fixed inset-0 z-90 bg-black/30 backdrop-blur-sm flex items-center justify-center p-4"
    >
        <div
            @click.away="open = false"
            x-show="open"
            x-transition
            class="bg-white rounded-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto p-8 relative"
        >
            <button
                type="button"
                @click="open = false"
                class="absolute top-6 right-6 w-9 h-9 flex items-center justify-center rounded-full hover:bg-zinc-100 transition"
                aria-label="{{ __('Close') }}"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 6l-12 12" /><path d="M6 6l12 12" />
                </svg>
            </button>

            <div class="flex gap-4 items-center mb-8 pr-8">
                @if($product->getFirstMediaUrl('gallery', 'thumb'))
                    <img
                        src="{{ $product->getFirstMediaUrl('gallery', 'thumb') }}"
                        alt="{{ $product->title }}"
                        class="w-16 h-16 object-cover rounded-lg shrink-0"
                    >
                @endif
                <div>
                    @if($product->collection)
                        <p class="text-xs uppercase tracking-[0.18em] text-zinc-400 mb-1">{{ $product->collection->title }}</p>
                    @endif
                    <p class="font-medium">{{ $product->title }}</p>
                </div>
            </div>

            <div
                class="xts-backorder-box"
                data-backorder-box
                data-product-id="{{ $product->id }}"
                data-nonce="{{ csrf_token() }}"
            >
                <h3 class="text-xl font-medium mb-2">
                    @if(app()->getLocale() == 'en')
                    Available on request
                    @endif
                    @if(app()->getLocale() == 'fr')
                        Disponible sur demande
                    @endif
                </h3>
                <p class="text-zinc-500 mb-6 text-sm font-normal">
                    @if(app()->getLocale() == 'en')
                    This product is available on backorder. Send us your details and we will confirm availability, timing and conditions.
                    @endif
                    @if(app()->getLocale() == 'fr')
                        Ce produit est disponible sur commande. Envoyez-nous vos coordonnées et nous vous confirmerons sa disponibilité, les délais et les conditions.
                    @endif
                </p>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div class="col-span-2 relative">
                        <x-tabler-user class="w-3.5 h-3.5 absolute left-4 top-1/2 -translate-y-1/2 text-zinc-400" stroke-width="1.5" />
                        <input type="text" name="full_name" placeholder="{{ __('Full name') }}" class="w-full pl-10 pr-4 py-3 border border-zinc-200 rounded-xl text-sm font-medium placeholder-zinc-400 focus:outline-none focus:ring-2 ring-black/20 transition-colors duration-200">
                    </div>

                    <div class="relative">
                        <x-tabler-phone class="w-3.5 h-3.5 absolute left-4 top-1/2 -translate-y-1/2 text-zinc-400" stroke-width="1.5" />
                        <input type="tel" name="phone" placeholder="{{ __('Phone number') }}" class="w-full pl-10 pr-4 py-3 border border-zinc-200 rounded-xl text-sm font-medium placeholder-zinc-400 focus:outline-none focus:ring-2 ring-black/20 transition-colors duration-200">
                    </div>

                    <div class="relative">
                        <x-tabler-mail class="w-3.5 h-3.5 absolute left-4 top-1/2 -translate-y-1/2 text-zinc-400" stroke-width="1.5" />
                        <input type="email" name="email" placeholder="{{ __('Email') }}" class="w-full pl-10 pr-4 py-3 border border-zinc-200 rounded-xl text-sm font-medium placeholder-zinc-400 focus:outline-none focus:ring-2 ring-black/20 transition-colors duration-200">
                    </div>

                    <div class="col-span-2 relative">
                        <x-tabler-ruler-2 class="w-3.5 h-3.5 absolute left-4 top-1/2 -translate-y-1/2 text-zinc-400" stroke-width="1.5" />
                        <input type="text" name="measurements" placeholder="{{ __('Measurements') }}" class="w-full pl-10 pr-4 py-3 border border-zinc-200 rounded-xl text-sm font-medium placeholder-zinc-400 focus:outline-none focus:ring-2 ring-black/20 transition-colors duration-200">
                    </div>
                </div>

                <div class="relative mb-4">
                    <x-tabler-message class="w-3.5 h-3.5 absolute left-4 top-4 text-zinc-400" stroke-width="1.5" />
                    <textarea name="comment" placeholder="{{ __('Comment') }}" rows="3" class="w-full pl-10 pr-4 py-3 border border-zinc-200 rounded-xl text-sm font-medium placeholder-zinc-400 focus:outline-none focus:ring-2 ring-black/20 transition-colors duration-200 resize-none"></textarea>
                </div>

                <input type="text" name="website" value="" class="xts-hidden-field absolute -left-[9999px]" tabindex="-1" autocomplete="off">

                <button
                    type="button"
                    class="xts-backorder-button w-full bg-black text-white rounded-xl py-4 hover:bg-zinc-800 transition"
                    data-backorder-submit
                >
                    {{ __('Send request') }}
                </button>

                <div class="xts-backorder-message text-sm text-zinc-500 mt-4" data-backorder-message></div>
            </div>
        </div>
    </div>
</div>
