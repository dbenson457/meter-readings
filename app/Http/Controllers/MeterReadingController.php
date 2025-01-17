<?php
namespace App\Http\Controllers;

use App\Models\Meter;
use App\Models\MeterReading;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Jobs\ProcessBulkUpload;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class MeterReadingController extends Controller
{
    public function store(Request $request, Meter $meter)
    {
        $validated = $request->validate([
            'reading_value' => 'required|integer',
            'reading_date' => 'required|date|before_or_equal:today',
        ]);

        // Validate the reading value
        $previousReading = $meter->readings()->where('reading_date', '<=', $validated['reading_date'])->orderBy('reading_date', 'desc')->first();
        if ($previousReading) {
            $estimatedReading = $meter->calculateEstimatedReading($previousReading->reading_value, $previousReading->reading_date, $validated['reading_date']);
            $minAcceptable = $estimatedReading * 0.75;
            $maxAcceptable = $estimatedReading * 1.25;

            if ($validated['reading_value'] < $minAcceptable || $validated['reading_value'] > $maxAcceptable) {
                return back()->withErrors(['reading_value' => 'The reading value is outside the acceptable range.'])->withInput();
            }
        }

        $meter->readings()->create($validated);

        return redirect()->route('meters.show', $meter)->with('success', 'Reading added successfully.');
    }

    public function estimate(Request $request, Meter $meter)
    {
        $validated = $request->validate([
            'estimate_date' => 'required|date|before_or_equal:today',
        ]);

        $previousReading = $meter->readings()->where('reading_date', '<=', $validated['estimate_date'])->orderBy('reading_date', 'desc')->first();
        if (!$previousReading) {
            return back()->withErrors(['estimate_date' => 'No previous reading found for estimation.'])->withInput();
        }

        $estimatedReadingValue = $meter->calculateEstimatedReading($previousReading->reading_value, $previousReading->reading_date, $validated['estimate_date']);

        $meter->readings()->create([
            'reading_value' => $estimatedReadingValue,
            'reading_date' => $validated['estimate_date'],
        ]);

        return redirect()->route('meters.show', $meter)->with('success', 'Estimated reading generated successfully.');
    }

    public function showBulkUploadForm()
    {
        $invalidLines = session('invalid_lines', null);
        Log::info('Showing bulk upload form', ['invalidLines' => $invalidLines]);
    
        return view('meters.bulk-upload', compact('invalidLines'));
    }

    public function bulkUpload(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $filePath = $request->file('csv_file')->store('uploads');
        $invalidLinesFilePath = 'private/uploads/invalid_lines_' . time() . '.txt';

        Log::info('Bulk upload initiated', ['filePath' => $filePath]);

        // Pass the full path to the job
        ProcessBulkUpload::dispatch(storage_path('app/private/' . $filePath), storage_path('app/' . $invalidLinesFilePath));

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

        return redirect()->route('meters.bulkUploadForm')->with('success', 'File has been processed successfully.')->with('invalid_lines', $invalidLines);
    }

   /*  public function deleteInvalidLines($filename)
    {
        $filePath = 'public/' . $filename;

        if (Storage::exists($filePath)) {
            Storage::delete($filePath);
            Log::info('Invalid lines file deleted', ['filePath' => $filePath]);
        } else {
            Log::warning('Invalid lines file not found for deletion', ['filePath' => $filePath]);
        }

        return redirect()->route('meters.bulkUploadForm')->with('success', 'Invalid lines file has been deleted.');
    } */
    public function destroy(Meter $meter, MeterReading $reading)
    {
        $reading->delete();
        return redirect()->route('meters.show', $meter)->with('success', 'Reading deleted successfully.');
    }
}