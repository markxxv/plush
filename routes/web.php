<?php

use App\Http\Controllers\Shop;
use App\Http\Controllers\StripeController;
use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;


Route::get('api/delivery-cost/{countryCode}', [
    Shop::class,
    'getDeliveryCost',
])->name('api.delivery.cost');

Route::post('payment/webhook', [
    StripeController::class,
    'webhook',
])->name('payment.webhook');

Route::post('api/request', [
    Shop::class,
    'preorder',
])->name('api.preorder');


$defaultLocale = config('localization.default');
$locales = config('localization.locales');

$orderedLocales = array_values(
    array_filter(
        $locales,
        fn (string $locale) => $locale !== $defaultLocale
    )
);

$orderedLocales[] = $defaultLocale;

foreach ($orderedLocales as $locale) {
    $routes = Route::middleware(
        SetLocale::class . ':' . $locale
    );

    if ($locale !== $defaultLocale) {
        $routes
            ->prefix($locale)
            ->name($locale . '.');
    }

    $routes->group(base_path('routes/storefront.php'));
}
