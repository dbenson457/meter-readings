@extends('layouts.app')

@section('content')
    <div class="mb-4">
        <h1 class="text-2xl font-bold mb-4">Meter Details</h1>
        <a href="{{ route('meters.index') }}" 
           class="text-blue-500 hover:text-blue-700">
            Back to Meters
        </a>
    </div>

    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <div class="mb-6">
            <h2 class="text-xl font-bold mb-4">Meter Information</h2>
            <p><strong>MPXN:</strong> {{ $meter->mpxn }}</p>
            <p><strong>Type:</strong> {{ ucfirst($meter->type) }}</p>
            <p><strong>Installation Date:</strong> {{ $meter->installation_date }}</p>
        </div>

        <div class="mb-6">
            <h2 class="text-xl font-bold mb-4">Add Reading</h2>
            <form action="{{ route('meter.readings.store', $meter) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="reading_value">
                        Reading Value (kWh)
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('reading_value') border-red-500 @enderror"
                           type="number" 
                           name="reading_value" 
                           id="reading_value" 
                           value="{{ old('reading_value') }}" 
                           required>
                    @error('reading_value')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="reading_date">
                        Reading Date
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('reading_date') border-red-500 @enderror"
                           type="date" 
                           name="reading_date" 
                           id="reading_date" 
                           value="{{ old('reading_date') }}" 
                           required>
                    @error('reading_date')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>

                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" 
                        type="submit">
                    Add Reading
                </button>
            </form>
        </div>

        <div>
            <h2 class="text-xl font-bold mb-4">Meter Readings</h2>
            @if($meter->readings->count() > 0)
                <table class="min-w-full">
                    <thead>
                        <tr>
                            <th class="text-left">Reading Value (kWh)</th>
                            <th class="text-left">Reading Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($meter->readings->sortByDesc('reading_date') as $reading)
                            <tr>
                                <td class="py-2">{{ $reading->reading_value }}</td>
                                <td>{{ $reading->reading_date }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-gray-600">No readings recorded yet.</p>
            @endif
        </div>
    </div>
@endsection