<?php

use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::controller('SiteController')->group(function () {
    Route::get('/', 'index')->name('home');
    Route::get('/products', 'products')->name('products');
    Route::get('/products/results', 'products')->name('products.results');
    Route::get('/categories', 'categories')->name('categories');
});

Route::get('/product/{product:slug}', [ProductController::class, 'show'])->name('product.show');
Route::post('/product/{product:slug}/cart', [ProductController::class, 'add'])->middleware('throttle:60,1')->name('product.cart');
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
Route::middleware(['auth', 'delete.status'])->group(function () {
    Route::get('/product/{product:slug}/review', [ProductController::class, 'reviewForm'])->name('product.review.form');
    Route::post('/product/{product:slug}/review', [ProductController::class, 'review'])->middleware('throttle:10,1')->name('product.review');
    Route::post('/product/{product:slug}/wishlist', [ProductController::class, 'wishlist'])->middleware('throttle:60,1')->name('product.wishlist');
    Route::post('/checkout/quote', [CheckoutController::class, 'quote'])->name('checkout.quote');
    Route::patch('/checkout/cart', [CheckoutController::class, 'updateCart'])->name('checkout.cart');
    Route::post('/checkout/address', [CheckoutController::class, 'address'])->middleware('throttle:30,1')->name('checkout.address');
    Route::post('/checkout/order', [CheckoutController::class, 'store'])->middleware('throttle:10,1')->name('checkout.store');
    Route::post('/checkout/deposit', [CheckoutController::class, 'deposit'])->middleware('throttle:5,1')->name('checkout.deposit');
    Route::get('/orders/{orderNo}/confirmation', [CheckoutController::class, 'confirmation'])->name('order.confirmation');
});
