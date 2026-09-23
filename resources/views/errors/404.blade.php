@section('metaTitle', '404 Page not found | Maison Plush Paris')
@section('metaDescription', '')

<x-layout>
    <!-- Hero Section -->
    <div class="relative py-24 lg:py-32 overflow-hidden">
        <!-- Background Elements -->
        <div class="absolute top-12 right-12 w-32 h-32 border border-zinc-100 rounded-full opacity-30"></div>
        <div class="absolute bottom-12 left-12 w-24 h-24 border border-zinc-200 rounded-full opacity-20"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 border border-zinc-50 rounded-full opacity-10"></div>

        <div class="container mx-auto px-4 md:px-8 relative z-10">
            <div class="text-center max-w-2xl mx-auto">
                <!-- 404 Number -->
                <div class="relative mb-8">
                    <p class="text-[12rem] md:text-[16rem] lg:text-[20rem] leading-none font-light text-zinc-200 select-none">404</p>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="w-20 h-[1px] bg-zinc-300"></div>
                    </div>
                </div>

                <!-- Error Message -->
                <h1 class="text-2xl md:text-3xl lg:text-4xl font-light text-zinc-900 mb-6 leading-[1.1]">
                    {{ __('This page is not found') }}
                </h1>

                <p class="text-lg text-zinc-600 font-light mb-12 leading-relaxed">

                    @if(app()->getLocale() == 'en')
                        The page might have been moved or deleted.
                    @endif
                </p>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="/" class="group relative px-8 py-4 bg-black text-white rounded-full overflow-hidden transition-all duration-300 hover:bg-zinc-800">
                        <span class="relative z-10 text-sm font-medium uppercase tracking-[0.2em]">{{ __('Home Page') }}</span>
                    </a>
                    @if(app()->getLocale() == 'en')
                        <a href="/contact" class="group relative px-8 py-4 border border-zinc-300 text-zinc-700 rounded-full overflow-hidden transition-all duration-300 hover:border-zinc-900 hover:text-zinc-900">
                            <span class="relative z-10 text-sm font-medium uppercase tracking-[0.2em]">{{ __('Contact Us') }}</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

</x-layout>
