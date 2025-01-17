<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessBulkUpload;
use App\Models\Meter;
use App\Models\MeterReading;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MeterReadingController extends Controller
{
    // Store a newly created meter reading in storage
    public function store(Request $request, Meter $meter)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'reading_value' => 'required|integer',
            'reading_date'  => 'required|date|before_or_equal:today',
        ]);

        // Validate the reading value against previous readings
        $previousReading = $meter->readings()->where('reading_date', '<=', $validated['reading_date'])
            ->orderBy('reading_date', 'desc')->first();
        if ($previousReading) {
            // Calculate the estimated reading based on previous reading
            $estimatedReading = $meter->calculateEstimatedReading(
                $previousReading->reading_value,
                $previousReading->reading_date,
                $validated['reading_date']
            );
            // Define acceptable range for the reading value
            $minAcceptable = $estimatedReading * 0.75;
            $maxAcceptable = $estimatedReading * 1.25;

            // Check if the reading value is within the acceptable range
            if ($validated['reading_value'] < $minAcceptable || $validated['reading_value'] > $maxAcceptable) {
                return back()->withErrors(
                    ['reading_value' => 'The reading value is outside the acceptable range.']
                )->withInput();
            }
        }

        // Create a new meter reading record with the validated data
        $meter->readings()->create($validated);

        // Redirect to the meter's show page with a success message
        return redirect()->route('meters.show', $meter)->with('success', 'Reading added successfully.');
    }

    // Estimate a meter reading based on previous readings
    public function estimate(Request $request, Meter $meter)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'estimate_date' => 'required|date|before_or_equal:today',
        ]);

        // Get the previous reading before the estimate date
        $previousReading = $meter->readings()
            ->where('reading_date', '<=', $validated['estimate_date'])
            ->orderBy('reading_date', 'desc')->first();
        if (!$previousReading) {
            return back()
                ->withErrors(['estimate_date' => 'No previous reading found for estimation.'])->withInput();
        }

        // Calculate the estimated reading value
        $estimatedReadingValue = $meter
            ->calculateEstimatedReading(
                $previousReading->reading_value,
                $previousReading->reading_date,
                $validated['estimate_date']
            );

        // Create a new meter reading record with the estimated data
        $meter->readings()->create([
            'reading_value' => $estimatedReadingValue,
            'reading_date'  => $validated['estimate_date'],
        ]);

        // Redirect to the meter's show page with a success message
        return redirect()->route('meters.show', $meter)->with('success', 'Estimated reading generated successfully.');
    }

    // Show the form for bulk uploading meter readings
    public function showBulkUploadForm()
    {
        // Get any invalid lines from the session
        $invalidLines = session('invalid_lines', null);
        Log::info('Showing bulk upload form', ['invalidLines' => $invalidLines]);

        // Return the meters.bulk-upload view with the invalid lines
        return view('meters.bulk-upload', compact('invalidLines'));
    }

    // Handle the bulk upload of meter readings
    public function bulkUpload(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        // Store the uploaded file
        $filePath = $request->file('csv_file')->store('uploads');
        $invalidLinesFilePath = 'private/uploads/invalid_lines_' . time() . '.txt';

        Log::info('Bulk upload initiated', ['filePath' => $filePath]);

        // Dispatch a job to process the bulk upload
        ProcessBulkUpload::dispatch(storage_path(
            'app/private/' . $filePath
        ), storage_path(
            'app/' . $invalidLinesFilePath
        ));

        // Wait for the job to complete and read the invalid lines file
        sleep(5); // Adjust the sleep time as needed to wait for the job to complete

        $fullInvalidLinesFilePath = storage_path('app/' . $invalidLinesFilePath);
        $invalidLines = null;
        if (file_exists($fullInvalidLinesFilePath)) {
            $invalidLines = file_get_contents($fullInvalidLinesFilePath);
            Log::info('Invalid lines file content', ['invalidLines' => $invalidLines]);
        } else {
            Log::warning('Invalid lines file not found', ['invalidLinesFilePath' => $fullInvalidLinesFilePath]);
        }

        Log::info('Redirecting to bulk upload form', ['invalidLines' => $invalidLines]);

        // Redirect to the bulk upload form with a success message and invalid lines
        return redirect()
            ->route('meters.bulkUploadForm')
            ->with('success', 'File has been processed successfully.')
            ->with('invalid_lines', $invalidLines);
    }

    // Remove the specified meter reading from storage
    public function destroy(Meter $meter, MeterReading $reading)
    {
        // Delete the meter reading record
        $reading->delete();
        // Redirect to the meter's show page with a success message
        return redirect()->route('meters.show', $meter)->with('success', 'Reading deleted successfully.');
    }
}
