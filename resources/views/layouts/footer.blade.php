<footer class="site_footer mt-12">
    <div class="container footer_grid">
        <article class="space-y-8 order-1 md:order-1">
            <p>
                <img src="/img/logo_white.svg" width="160" alt="Maison Plush Paris">
            </p>
            <p class="font-medium">Maison Plush Paris</p>
            <p>
                @if(app()->getLocale() == 'en')
                Fashion fades, but the spirit is weightless, timeless, and unstoppable—just like true style
                @endif
                @if(app()->getLocale() == 'fr')
                La mode passe, mais l’esprit reste léger, intemporel et inarrêtable — à l’image du véritable style
                @endif
            </p>
        </article>
        <article class="flex gap-12 lg:gap-24 order-3 md:order-2">
            <nav>
                <p class="title">{{ __('Information') }}</p>
                <a href="{{ localized_route('home') }}/about">{{ __('About Us') }}</a>
                <a href="{{ localized_route('home') }}/contact">{{ __('Contact Us') }}</a>
                <a href="{{ localized_route('home') }}/privacy-policy">{{ __('Privacy Policy') }}</a>
                <a href="{{ localized_route('home') }}/disclaimer">{{ __('Disclaimer') }}</a>
            </nav>

            <nav>
                <p class="title">{{ __('Collections') }}</p>
                @foreach (\App\Models\ProductCollection::where('active', true)->orderBy('position')->get() as $collection)
                    <a href="{{ localized_route('home') }}/collections/{{ $collection->url }}">{{ $collection->title }}</a>
                @endforeach
            </nav>
        </article>
        <article class="md:text-right space-y-8 order-2 md:order-3">
            <p class="font-medium">SAS PLUSH</p>
            <p>
                66 Avenue Champs-elysées<br/>
                75008 Paris<br/>
                Siren : 931 464 648 <br/>
                plush.maison.official@gmail.com
            </p>
        </article>
    </div>

    <p class="container text-neutral-500 space-x-4 text-sm mt-6">
        <a href="{{ localized_route('home') }}/cookies-policy">{{ __('Cookie Policy') }}</a>
        <a href="{{ localized_route('home') }}/terms-and-conditions">{{ __('Terms and Conditions') }}</a>
        <a href="{{ localized_route('home') }}/privacy-policy">{{ __('Privacy Policy') }}</a>
    </p>
</footer>

<x-search />
