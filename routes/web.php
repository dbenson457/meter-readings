<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MeterController;
use App\Http\Controllers\MeterReadingController;

Route::get('/', function () {
    return redirect()->route('meters.index');
});

Route::resource('meters', MeterController::class);
Route::post('meters/{meter}/readings', [MeterReadingController::class, 'store'])
    ->name('meter.readings.store');