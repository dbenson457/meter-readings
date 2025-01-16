<?php

namespace App\Http\Controllers;

use App\Models\Meter;
use Illuminate\Http\Request;

class MeterController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $type = $request->input('type');
        $sort = $request->input('sort', 'asc');
    
        $meters = Meter::query()
            ->when($search, function ($query, $search) {
                return $query->where('mpxn', 'like', "%{$search}%");
            })
            ->when($type, function ($query, $type) {
                return $query->where('type', $type);
            })
            ->orderBy('installation_date', $sort)
            ->get();
    
        return view('meters.index', compact('meters'));
    }

    public function create()
    {
        return view('meters.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'mpxn' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    if (!preg_match('/^S\d{21}$/', $value) && !preg_match('/^\d{6,10}$/', $value)) {
                        $fail('The ' . $attribute . ' must be a valid MPAN (Electricity) or MPRN (Gas).');
                    }
                },
            ],
            'type' => 'required|string|in:electricity,gas',
            'installation_date' => 'required|date',
            'estimated_annual_consumption' => 'required|integer|between:2000,8000',
        ]);

        // Ensure the type matches the MPXN format
        if ((preg_match('/^S\d{21}$/', $validated['mpxn']) && $validated['type'] !== 'electricity') ||
            (preg_match('/^\d{6,10}$/', $validated['mpxn']) && $validated['type'] !== 'gas')) {
            return back()->withErrors(['mpxn' => 'The MPXN does not match the selected type.'])->withInput();
        }

        Meter::create($validated);

        return redirect()->route('meters.index')->with('success', 'Meter created successfully.');
    }

    public function show(Meter $meter)
    {
        return view('meters.show', compact('meter'));
    }

    public function destroy(Meter $meter)
    {
        $meter->delete();
        return redirect()->route('meters.index')->with('success', 'Meter deleted successfully.');
    }
}
