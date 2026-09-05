<?php

use Illuminate\Support\Facades\Route;

Route::namespace('User\Auth')->name('user.')->middleware('guest')->group(function () {
    Route::controller('LoginController')->group(function () {
        Route::get('/login', 'showLoginForm')->name('login');
        Route::post('/login', 'login');
        Route::get('logout', 'logout')->middleware('auth')->withoutMiddleware('guest')->name('logout');
    });

    Route::controller('RegisterController')->group(function () {
        Route::get('register', 'showRegistrationForm')->name('register');
        Route::post('register', 'register');
        Route::post('check-user', 'checkUser')->name('checkUser')->withoutMiddleware('guest');
    });
});

Route::middleware('auth', 'delete.status')->name('user.')->group(function () {
    // Route::middleware(['check.status'])->group(function () {
        Route::namespace('User')->group(function () {


            Route::controller('UserController')->group(function () {
                Route::get('dashboard', 'home')->name('dashboard');
            });


            
        });
    // });
});
