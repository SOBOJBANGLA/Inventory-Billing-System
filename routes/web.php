<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('app');
});

// Test route to check if API is working
Route::get('/test-api', function () {
    return response()->json([
        'message' => 'API is working',
        'timestamp' => now()
    ]);
});

Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');