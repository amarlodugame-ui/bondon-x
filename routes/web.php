<?php

use Illuminate\Support\Facades\Route;


Route::controller('SiteController')->group(function () {
    
    Route::get('/', 'index')->name('home');
    

});

