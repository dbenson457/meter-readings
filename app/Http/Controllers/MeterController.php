<?php

namespace App\Http\Controllers;

use App\Models\Meter;
use Illuminate\Http\Request;

class MeterController extends Controller
{
    // Display a listing of the meters
    public function index(Request $request)
    {
        // Get search, type, and sort parameters from the request
        $search = $request->input('search');
        $type = $request->input('type');
        $sort = $request->input('sort', 'asc');

        // Query the Meter model with optional search and type filters, and sort by installation date
        $meters = Meter::query()
            ->when($search, function ($query, $search) {
                // Filter by 'mpxn' field if search parameter is provided
                return $query->where('mpxn', 'like', "%{$search}%");
            })
            ->when($type, function ($query, $type) {
                // Filter by 'type' field if type parameter is provided
                return $query->where('type', $type);
            })
            ->orderBy('installation_date', $sort) // Sort by installation date
            ->get(); // Get the results

        // Return the meters.index view with the list of meters
        return view('meters.index', compact('meters'));
    }

    // Show the form for creating a new meter
    public function create()
    {
        // Return the meters.create view
        return view('meters.create');
    }

    // Store a newly created meter in storage
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'mpxn' => [
                'required',
                'string',
                'max:255',
                // Custom validation rule for MPXN format
                function ($attribute, $value, $fail) {
                    if (!preg_match('/^S\d{21}$/', $value) && !preg_match('/^\d{6,10}$/', $value)) {
                        $fail('The ' . $attribute . ' must be a valid MPAN (Electricity) or MPRN (Gas).');
                    }
                },
            ],
            'type'                         => 'required|string|in:electricity,gas',
            'installation_date'            => 'required|date',
            'estimated_annual_consumption' => 'required|integer|between:2000,8000',
        ]);

        // Ensure the type matches the MPXN format
        if (
            (preg_match('/^S\d{21}$/', $validated['mpxn']) && $validated['type'] !== 'electricity') ||
            (preg_match('/^\d{6,10}$/', $validated['mpxn']) && $validated['type'] !== 'gas')
        ) {
            // Return back with an error if the type does not match the MPXN format
            return back()->withErrors(['mpxn' => 'The MPXN does not match the selected type.'])->withInput();
        }

        // Create a new meter record with the validated data
        Meter::create($validated);

        // Redirect to the meters index with a success message
        return redirect()->route('meters.index')->with('success', 'Meter created successfully.');
    }

    // Display the specified meter
    public function show(Meter $meter)
    {
        // Return the meters.show view with the meter data
        return view('meters.show', compact('meter'));
    }

    // Remove the specified meter from storage
    public function destroy(Meter $meter)
    {
        // Delete the meter record
        $meter->delete();
        // Redirect to the meters index with a success message
        return redirect()->route('meters.index')->with('success', 'Meter deleted successfully.');
    }
}
