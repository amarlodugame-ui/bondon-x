<?php

use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;




$pendingPage = fn () => abort(501, 'This page is not implemented yet.');

if (!Route::has('home')) {
    Route::get('/', $pendingPage)->name('home');
}

if (!Route::has('products')) {
    Route::get('/products', $pendingPage)->name('products');
}

if (!Route::has('products.results')) {
    Route::get('/products/results', $pendingPage)->name('products.results');
}

if (!Route::has('products.suggestions')) {
    Route::get('/products/suggestions', $pendingPage)->name('products.suggestions');
}

if (!Route::has('categories')) {
    Route::get('/categories', $pendingPage)->name('categories');
}

if (!Route::has('product.show')) {
    Route::get('/product/{product}', $pendingPage)->name('product.show');
}



if (!Route::has('installment.products')) {
    Route::get('/installment-products', $pendingPage)->name('installment.products');
}

if (!Route::has('installment.guide')) {
    Route::get('/installment-guide', $pendingPage)->name('installment.guide');
}

if (!Route::has('cart.index')) {
    Route::get('/cart', $pendingPage)->name('cart.index');
}

if (!Route::has('about')) {
    Route::get('/about-us', $pendingPage)->name('about');
}

if (!Route::has('user.login')) {
    Route::get('/login', $pendingPage)->name('user.login');
}

if (!Route::has('user.register')) {
    Route::get('/register', $pendingPage)->name('user.register');
}

if (!Route::has('user.password.request')) {
    Route::get('/forgot-password', $pendingPage)->name('user.password.request');
}


    Route::controller('SupportController')->group(function () {
        Route::get('/support', 'index')->name('support');
        Route::get('/faq', 'faq')->name('faq');
        Route::get('/membership', 'membership')->name('membership');
        Route::get('/return-refund-policy', 'returnRefund')->name('policy.return_refund');
        Route::get('/shipping-delivery-policy', 'shipping')->name('policy.shipping');
        Route::get('/contact-us', 'contact')->name('contact');
        Route::post('/contact-us', 'sendContact')->middleware('throttle:5,1')->name('contact.submit');
        Route::get('/terms-and-conditions', 'terms')->name('policy.terms');
        Route::get('/privacy-policy', 'privacy')->name('policy.privacy');
    });


    Route::controller('OfferController')->group(function () {
        Route::get('/offers', 'index')->name('offers');
    });


Route::controller('SiteController')->group(function () {
    Route::get('/', 'index')->name('home');
    Route::get('/products', 'products')->name('products');
    Route::get('/products/results', 'products')->name('products.results');
    Route::get('/products/suggestions', 'productSuggestions')->name('products.suggestions');
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
