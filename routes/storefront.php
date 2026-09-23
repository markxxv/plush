<?php

use App\Http\Controllers\Front;
use App\Http\Controllers\Shop;
use App\Http\Controllers\StripeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [Front::class, 'home'])->name('home');

Route::view('about', 'pages.about')->name('about');
Route::view('contact', 'pages.contact')->name('contact');

Route::get('shop', [Shop::class, 'index'])->name('shop');
Route::get('fashion/{url}', [Shop::class, 'product'])->name('shop.item');

Route::get('collections', [Shop::class, 'collections'])->name('collections');
Route::get('collections/{url}', [Shop::class, 'collection'])->name('collection');

Route::get('checkout', [Shop::class, 'checkout'])->name('checkout');
Route::post('checkout', [Shop::class, 'processOrder'])->name('checkout.process');

Route::get('order/{orderNumber}', [Shop::class, 'orderStatus'])
    ->name('order.status');

Route::prefix('payment')->name('payment.')->group(function () {
    Route::post('create-session', [StripeController::class, 'createSession'])
        ->name('create-session');

    Route::get('success', [StripeController::class, 'success'])
        ->name('success');

    Route::get('cancel', [StripeController::class, 'cancel'])
        ->name('cancel');
});

Route::get('/api/search', [Front::class, 'search'])
    ->name('api.search');

Route::get('wishlist', [Shop::class, 'wishlist'])->name('wishlist');

// Всегда последним
Route::get('{url}', [Front::class, 'getPage'])->name('page');
