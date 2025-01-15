<?php

namespace App\Http\Controllers;

use App\Models\Meter;
use App\Models\MeterReading;
use Illuminate\Http\Request;

class MeterReadingController extends Controller
{
    public function store(Request $request, Meter $meter)
    {
        $validated = $request->validate([
            'reading_value' => 'required|integer',
            'reading_date' => 'required|date|before_or_equal:today',
        ]);

        $meter->readings()->create($validated);

        return redirect()->route('meters.show', $meter)
            ->with('success', 'Reading added successfully.');
    }
}
