@php
    use Carbon\Carbon;
@endphp

@extends('layouts.app')

@section('content')
<div class="mb-6">
    <a href="{{ route('meters.index') }}" 
       class="inline-flex items-center text-gray-500 hover:text-gray-700">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Back to Meters
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Meter Information -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Meter Information</h2>
            <div class="space-y-4">
                <div>
                    <label class="text-sm font-medium text-gray-500">MPXN</label>
                    <p class="text-lg font-semibold text-gray-800">{{ $meter->mpxn }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Type</label>
                    <p class="mt-1">
                        <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full 
                            {{ $meter->type === 'electricity' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                            {{ ucfirst($meter->type) }}
                        </span>
                    </p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Installation Date</label>
                    <p class="text-lg font-semibold text-gray-800">
                        {{ \Carbon\Carbon::parse($meter->installation_date)->format('M d, Y') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Add Reading Form -->
        <div class="bg-white rounded-lg shadow-md p-6 mt-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Add Reading</h2>
            <form action="{{ route('meter.readings.store', $meter) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700" for="reading_value">
                        Reading Value (kWh)
                    </label>
                    <input type="number" 
                           name="reading_value" 
                           id="reading_value"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                           value="{{ old('reading_value') }}" 
                           required>
                    @error('reading_value')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700" for="reading_date">
                        Reading Date
                    </label>
                    <input type="date" 
                           name="reading_date" 
                           id="reading_date"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                           value="{{ old('reading_date') }}" 
                           required>
                    @error('reading_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                        class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-300">
                    Add Reading
                </button>
            </form>
        </div>
    </div>

    <!-- Readings History -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-800">Reading History</h2>
                <div class="flex space-x-2">
                    <span class="text-sm text-gray-500">Total Readings: {{ $meter->readings->count() }}</span>
                </div>
            </div>
            
            @if($meter->readings->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reading Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Value (kWh)</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Change</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Added On</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @php
                                $readings = $meter->readings->sortByDesc('reading_date');
                                $previousValue = null;
                            @endphp
                            @foreach($readings as $reading)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ \Carbon\Carbon::parse($reading->reading_date)->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ number_format($reading->reading_value) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($previousValue !== null)
                                            @php
                                                $change = $previousValue - $reading->reading_value;
                                                $changePercent = ($change / $reading->reading_value) * 100;
                                            @endphp
                                            <span class="{{ $change > 0 ? 'text-green-600' : 'text-red-600' }}">
                                                {{ $change > 0 ? '+' : '' }}{{ number_format($change) }}
                                                <span class="text-gray-500 text-xs">({{ number_format($changePercent, 1) }}%)</span>
                                            </span>
                                        @else
                                            <span class="text-gray-500">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $reading->created_at->diffForHumans() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <form action="{{ route('meter.readings.destroy', [$meter, $reading]) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this reading?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                @php
                                    $previousValue = $reading->reading_value;
                                @endphp
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No readings</h3>
                    <p class="mt-1 text-sm text-gray-500">Start by adding a new reading using the form.</p>
                </div>
            @endif
        </div>

        <!-- Usage Stats -->
        @if($meter->readings->count() > 1)
            <div class="bg-white rounded-lg shadow-md p-6 mt-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Usage Statistics</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Usage Stats -->
                @if($meter->readings->count() > 1)
                    <div class="bg-white rounded-lg shadow-md p-6 mt-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Usage Statistics</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            @php
                                $readings = $meter->readings->sortBy('reading_date');
                                $firstReading = $readings->first();
                                $lastReading = $readings->last();
                                $totalUsage = $lastReading->reading_value - $firstReading->reading_value;
                                $firstReadingDate = Carbon::parse($firstReading->reading_date);
                                $lastReadingDate = Carbon::parse($lastReading->reading_date);
                                $daysDiff = $firstReadingDate->diffInDays($lastReadingDate) ?: 1;
                                $avgDaily = $totalUsage / $daysDiff;
                            @endphp
                            <div class="stat-card rounded-lg p-4">
                                <p class="text-sm text-gray-500">Total Usage</p>
                                <p class="text-2xl font-bold text-gray-900">{{ number_format($totalUsage) }} kWh</p>
                            </div>
                        </div>
                    </div>
                @endif
                    <div class="stat-card rounded-lg p-4">
                        <p class="text-sm text-gray-500">Total Usage</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($totalUsage) }} kWh</p>
                    </div>
                    <div class="stat-card rounded-lg p-4">
                        <p class="text-sm text-gray-500">Average Daily</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($avgDaily, 1) }} kWh</p>
                    </div>
                    <div class="stat-card rounded-lg p-4">
                        <p class="text-sm text-gray-500">Date Range</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $daysDiff }} days</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection