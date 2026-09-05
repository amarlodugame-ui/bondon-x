<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/section-data/maintenance', function () {
    return response()->json([
        'status' => 'success',
        'maintenance_mode' => 'our application is currently in maintenance mode.',
    ]);
});


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
 
