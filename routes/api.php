<?php

use App\Http\Controllers\Api\DatabaseResetController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::name('api.')->group(function () {
    Route::post('database/reset', DatabaseResetController::class)
        // ->middleware('throttle:1,1')
        ->name('database.reset');

    Route::apiResource('products', ProductController::class);
});
