@section('metaTitle', $page->meta_title ?? $page->title)
@section('metaDescription', $page->meta_description ?? '')
@section('body_class', 'white_header')

<x-layout>


    <header class="page_header">
        <div class="inner">
            <div class="container">
                <h1>{{ $page->title }}</h1>
                <nav class="breadcrumbs">
                    <a href="/">{{ __('Home') }}</a> /
                    <span>{{ $page->title }}</span>
                </nav>
            </div>
        </div>
    </header>

    <section class="container grid grid-cols-[200px_auto] md:py-6 lg:py-12">
        <aside>
{{--            <nav class="flex flex-col text-sm space-y-2">--}}
{{--                <a href="">Disclaimer</a>--}}
{{--                <a href="">Privacy Statement</a>--}}
{{--                <a href="">Cookie Policy</a>--}}
{{--                <a href="">Privacy Policy</a>--}}
{{--                <a href="">CGV</a>--}}
{{--            </nav>--}}
        </aside>
        <div id="cmplz-document" class="prose">
            {!! $page->body !!}
        </div>
    </section>
</x-layout>
