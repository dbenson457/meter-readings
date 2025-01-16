<?php
namespace App\Http\Controllers;

use App\Models\Meter;
use App\Models\MeterReading;
use Illuminate\Http\Request;
use Carbon\Carbon;

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

    public function destroy(Meter $meter, MeterReading $reading)
    {
        $reading->delete();
        return redirect()->route('meters.show', $meter)->with('success', 'Reading deleted successfully.');
    }
}