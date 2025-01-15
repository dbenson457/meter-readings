<?php

namespace App\Http\Controllers;

use App\Models\Meter;
use Illuminate\Http\Request;

class MeterController extends Controller
{
    public function index()
    {
        $meters = Meter::all();
        return view('meters.index', compact('meters'));
    }

    public function create()
    {
        return view('meters.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'mpxn' => 'required|unique:meters,mpxn|string',
            'installation_date' => 'required|date',
            'type' => 'required|in:electricity,gas',
        ]);

        Meter::create($validated);

        return redirect()->route('meters.index')
            ->with('success', 'Meter created successfully.');
    }

    public function show(Meter $meter)
    {
        return view('meters.show', compact('meter'));
    }
}
