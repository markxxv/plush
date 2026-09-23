@section('metaTitle', 'Contact | Maison Plush Paris')
@section('metaDescription', '')

<x-layout>
    <section class="py-32">
        <div class="container grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-center">


            <div class="lg:col-span-5">
                <h1 class="font-medium text-4xl">{{ __('Contact Us') }}</h1>
{{--                <div class="py-7 border-b border-black/[0.07]">--}}
{{--                    <p class="text-[9px] tracking-[0.28em] uppercase text-black/30 mb-4 m-0">--}}
{{--                        {{ __('Phone') }}--}}
{{--                    </p>--}}
{{--                    <a href="tel:+34663266060 " class="c-value" style="font-size:clamp(24px,2.6vw,32px); line-height:1.2;">--}}
{{--                        <span class="text-gold/70" style="font-size:0.7em;">+34</span> 663 266 060--}}
{{--                    </a>--}}
{{--                    <p class="text-[11px] font-light text-black/30 mt-2 m-0">--}}
{{--                        Ежедневно, 9:00 — 21:00--}}
{{--                    </p>--}}
{{--                </div>--}}


                <div class="py-7 border-b border-zinc-200">
                    <p class="text-sm tracking-[0.28em] uppercase text-black/30 mb-4 m-0">
                        E-mail
                    </p>
                    <a href="mailto:plush.maison.official@gmail.com" translate="no" class="c-value break-all" style="font-size:clamp(19px,2vw,24px); line-height:1.25;">
                        plush.maison.official@gmail.com
                    </a>
                </div>


                <div class="py-7 border-b border-zinc-200">
                    <p class="text-sm tracking-[0.28em] uppercase text-black/30 mb-4 m-0">
                        Paris
                    </p>
                    <a href="https://maps.app.goo.gl/Ke8FUky398rqXjdj7" rel="nofollow" target="_blank" class="group inline-flex items-start gap-4 no-underline">
                            <span class="text-[18px] md:text-[20px] font-light text-black leading-snug group-hover:text-black/55 transition-colors duration-300">
                                66 Avenue Champs-elysées<br/> 75008 Paris
                            </span>
                        <span class="w-7 h-7 mt-1 flex items-center justify-center shrink-0 bg-white rounded text-black/50
                                         group-hover:border-gold group-hover:text-gold transition-all duration-300">
                                <svg width="10" height="8" viewBox="0 0 14 10" fill="none"><path d="M1 5H13M9 1L13 5L9 9" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                            </span>
                    </a>
                </div>


                <div class="pt-7">
                    <p class="text-[9px] tracking-[0.28em] uppercase text-black/30 mb-5 m-0">
                        {{ __('Our links') }}
                    </p>
                    <div class="flex items-center gap-2.5 flex-wrap">

                        <a href="https://www.instagram.com/i.am.plush/" target="_blank" rel="nofollow" class="c-social rounded" aria-label="Instagram">
                            <svg stroke-width="1.4" class="w-[19px] h-[19px]" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M4 8a4 4 0 0 1 4 -4h8a4 4 0 0 1 4 4v8a4 4 0 0 1 -4 4h-8a4 4 0 0 1 -4 -4l0 -8"></path>
                                <path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0"></path>
                                <path d="M16.5 7.5v.01"></path>
                            </svg>                            </a>
                        <a href="https://fr.linkedin.com/in/plush-production-3537a8390" target="_blank" rel="nofollow" class="c-social rounded" aria-label="LinkedIn">
                            <svg stroke-width="1.4" class="w-[19px] h-[19px]" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M8 11v5"></path>
                                <path d="M8 8v.01"></path>
                                <path d="M12 16v-5"></path>
                                <path d="M16 16v-3a2 2 0 1 0 -4 0"></path>
                                <path d="M3 7a4 4 0 0 1 4 -4h10a4 4 0 0 1 4 4v10a4 4 0 0 1 -4 4h-10a4 4 0 0 1 -4 -4l0 -10"></path>
                            </svg>                            </a>
{{--                        <a href="https://www.facebook.com/people/GG-Real-Estate-Barcelona/61556005752810/" target="_blank" rel="nofollow" class="c-social rounded" aria-label="Facebook">--}}
{{--                            <svg stroke-width="1.4" class="w-[19px] h-[19px]" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">--}}
{{--                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>--}}
{{--                                <path d="M7 10v4h3v7h4v-7h3l1 -4h-4v-2a1 1 0 0 1 1 -1h3v-4h-3a5 5 0 0 0 -5 5v2h-3"></path>--}}
{{--                            </svg>                            </a>--}}
                    </div>
                </div>
            </div>


            <div class="lg:col-span-7">
                <a href="https://maps.app.goo.gl/Ke8FUky398rqXjdj7" rel="nofollow" target="_blank" class="group block no-underline">
                    <div class="relative overflow-hidden rounded-lg bg-black/[0.03]">
                        <div class="aspect-[4/3] lg:aspect-[3/2] overflow-hidden">
                            <img class="w-full h-full grayscale-100 object-cover transition-transform hover:grayscale-0 group-hover:scale-[1.04] duration-400" style="transition-timing-function: cubic-bezier(0.25,0.46,0.45,0.94);" src="/img/map.webp" alt="Maison Plush Paris" loading="lazy">
                        </div>

                        <div class="absolute inset-0 bg-gradient-to-t from-black/25 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>

                        <div class="absolute bottom-5 right-5">
                                <span class="inline-flex items-center gap-2.5 bg-white/90 backdrop-blur-sm px-5 py-3
                                             text-[10px] tracking-[0.18em] uppercase text-black/70
                                             group-hover:text-gold transition-colors duration-300 rounded">
                                    {{ __('Open map') }}
                                    <svg width="10" height="8" viewBox="0 0 14 10" fill="none"><path d="M1 5H13M9 1L13 5L9 9" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                                </span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between mt-3 px-0.5">
                        <span class="text-[9px] tracking-[0.22em] uppercase text-black/25">66 Avenue Champs-elysées · Paris</span>
                        <span class="text-[9px] tracking-[0.18em] uppercase text-gold/70">Google Maps</span>
                    </div>
                </a>
            </div>
        </div>
    </section>
</x-layout>
