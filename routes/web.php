<?php

use App\Http\Controllers\MeterController;
use App\Http\Controllers\MeterReadingController;
use Illuminate\Support\Facades\Route;

// Redirect the root URL to the meters index page
Route::get('/', function () {
    return redirect()->route('meters.index');
});

// Show the form for bulk uploading meter readings
Route::get('/meters/bulk-upload', [MeterReadingController::class, 'showBulkUploadForm'])->name('meters.bulkUploadForm');

// Handle the bulk upload of meter readings
Route::post('/meters/bulk-upload', [MeterReadingController::class, 'bulkUpload'])->name('meters.bulkUpload');

/* Route to delete invalid lines from the bulk upload
Route::get('/delete-invalid-lines/{filename}',
[MeterReadingController::class,
'deleteInvalidLines'])
->name('deleteInvalidLines'); */

// Display a listing of the meters
Route::get('/meters', [MeterController::class, 'index'])->name('meters.index');

// Show the form for creating a new meter
Route::get('/meters/create', [MeterController::class, 'create'])->name('meters.create');

// Store a newly created meter in storage
Route::post(
    '/meters',
    [MeterController::class,
    'store']
)->name('meters.store');

// Display the specified meter
Route::get('/meters/{meter}', [MeterController::class, 'show'])->name('meters.show');

// Store a newly created meter reading in storage
Route::post('meters/{meter}/readings', [MeterReadingController::class, 'store'])->name('meter.readings.store');

// Estimate a meter reading based on previous readings
Route::post(
    'meters/{meter}/readings/estimate',
    [MeterReadingController::class, 'estimate']
)
->name('meter.readings.estimate');

// Remove the specified meter from storage
Route::delete('/meters/{meter}', [MeterController::class, 'destroy'])->name('meters.destroy');

// Remove the specified meter reading from storage
Route::delete(
    '/meters/{meter}/readings/{reading}',
    [MeterReadingController::class, 'destroy']
)
->name('meter.readings.destroy');
