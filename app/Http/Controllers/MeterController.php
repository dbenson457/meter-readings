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

    public function destroy(Meter $meter)
    {
        $meter->delete();
        return redirect()->route('meters.index')->with('success', 'Meter deleted successfully.');
    }
}
