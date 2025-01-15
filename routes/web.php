<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MeterController;
use App\Http\Controllers\MeterReadingController;

Route::get('/', function () {
    return redirect()->route('meters.index');
});

Route::get('/meters', [MeterController::class, 'index'])->name('meters.index');
Route::get('/meters/create', [MeterController::class, 'create'])->name('meters.create'); // Add this line
Route::post('/meters', [MeterController::class, 'store'])->name('meters.store'); // Add this line
Route::get('/meters/{meter}', [MeterController::class, 'show'])->name('meters.show');
Route::post('meters/{meter}/readings', [MeterReadingController::class, 'store'])->name('meter.readings.store');
Route::delete('/meters/{meter}', [MeterController::class, 'destroy'])->name('meters.destroy');
Route::delete('/meters/{meter}/readings/{reading}', [MeterReadingController::class, 'destroy'])->name('meter.readings.destroy');