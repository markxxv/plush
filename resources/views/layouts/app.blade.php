<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('metaTitle')</title>
    <meta name="description" content="@yield('metaDescription')" />

    <meta name="theme-color" content="#000000">

    <link rel="icon" href="/favicon.ico" sizes="32x32">
    <link rel="icon" href="/img/favicon.png" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/img/apple-touch-icon.png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100..700;1,100..700&family=Onest:wght@100..900&display=swap" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <link rel="manifest" href="/manifest.webmanifest">
    @vite('resources/css/app.css')

    <style>
        .site_header a {
            font-family: 'Jost', sans-serif!important;
        }
    </style>

</head>
<body class="@if(request()->routeIs('home', 'fr.home')) home_page @else not_home @endif @yield('body_class')">
    @include('layouts.header')
    {{ $slot }}
    @include('layouts.footer')
    <x-mobile-menu />

    <template id="storefront-toast-template">
        <div class="alert_box__icon" aria-hidden="true">
            <x-tabler-circle-check class="size-5" stroke-width="1.7" />
        </div>

        <div class="alert_box__content">
            <span class="alert_box__label">{{ __('Added') }}</span>
            <span class="alert_box__message" data-toast-message></span>
        </div>

        <button
            type="button"
            class="alert_box__close"
            data-toast-close
            aria-label="{{ __('Close notification') }}"
        >
            <x-tabler-x class="size-5" stroke-width="1.7" />
        </button>

        <span class="alert_box__progress" aria-hidden="true"></span>
    </template>

    @vite(['resources/js/app.js'])
</body>
</html>
