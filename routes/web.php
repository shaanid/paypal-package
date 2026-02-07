<?php

use Shaanid\PayPal\Http\Controllers\PayPalController;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function () {
    Route::controller(PayPalController::class)->prefix('paypal')->name('paypal.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('process', 'process')->name('process');
        Route::get('success', 'success')->name('success');
        Route::get('cancel', 'cancel')->name('cancel');
    });
});
