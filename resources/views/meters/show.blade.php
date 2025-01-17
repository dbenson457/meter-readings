@extends('layouts.app')

@section('content')
<div class="mb-6">
    <!-- Back to Meters link -->
    <a href="{{ route('meters.index') }}" class="inline-flex items-center text-gray-500 hover:text-gray-700">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
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
                    <!-- Display MPXN -->
                    <label class="text-sm font-medium text-gray-500">MPXN</label>
                    <p class="text-lg font-semibold text-gray-800">{{ $meter->mpxn }}</p>
                </div>
                <div>
                    <!-- Display Type -->
                    <label class="text-sm font-medium text-gray-500">Type</label>
                    <p class="mt-1">
                        <span
                            class="px-3 py-1 inline-flex text-sm font-semibold rounded-full {{ $meter->type === 'electricity' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                            {{ ucfirst($meter->type) }}
                        </span>
                    </p>
                </div>
                <div>
                    <!-- Display Installation Date -->
                    <label class="text-sm font-medium text-gray-500">Installation Date</label>
                    <p class="text-lg font-semibold text-gray-800">
                        {{ \Carbon\Carbon::parse($meter->installation_date)->format('M d, Y') }}</p>
                </div>
                <div>
                    <!-- Display Estimated Annual Consumption -->
                    <label class="text-sm font-medium text-gray-500">Estimated Annual Consumption</label>
                    <p class="text-lg font-semibold text-gray-800">{{ $meter->estimated_annual_consumption }} kWh</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Reading Form -->
    <div class="lg:col-span-2">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Add Reading</h2>
                <form action="{{ route('meter.readings.store', $meter) }}" method="POST" id="addReadingForm">
                    @csrf
                    <div class="mb-4">
                        <!-- Label for Reading Value input -->
                        <label class="block text-sm font-medium text-gray-700" for="reading_value">
                            Reading Value (kWh)
                        </label>
                        <!-- Reading Value input field -->
                        <input type="number" name="reading_value" id="reading_value"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                            value="{{ old('reading_value') }}" required>
                        <!-- Error message for Reading Value input -->
                        @error('reading_value')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <!-- Label for Reading Date input -->
                        <label class="block text-sm font-medium text-gray-700" for="reading_date">
                            Reading Date
                        </label>
                        <!-- Reading Date input field -->
                        <input type="date" name="reading_date" id="reading_date"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                            value="{{ old('reading_date') }}" required>
                        <!-- Error message for Reading Date input -->
                        @error('reading_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit button for adding the reading -->
                    <button type="submit"
                        class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-300">
                        Add Reading
                    </button>
                </form>
            </div>

            <!-- Estimated Reading Form -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Generate Estimated Reading</h2>
                <form action="{{ route('meter.readings.estimate', $meter) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <!-- Label for Estimate Date input -->
                        <label class="block text-sm font-medium text-gray-700" for="estimate_date">
                            Estimate Date
                        </label>
                        <!-- Estimate Date input field -->
                        <input type="date" name="estimate_date" id="estimate_date"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                            value="{{ old('estimate_date') }}" required>
                        <!-- Error message for Estimate Date input -->
                        @error('estimate_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit button for generating the estimated reading -->
                    <button type="submit"
                        class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-300">
                        Generate Estimated Reading
                    </button>
                </form>
            </div>
        </div>

        <!-- Readings History -->
        <div class="bg-white rounded-lg shadow-md p-6 mt-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Reading History</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Reading Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Value (kWh)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Change</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Added On</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Action</th>
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
                                $change = $reading->reading_value - $previousValue;
                                $changePercent = ($change / $previousValue) * 100;
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
                                <form action="{{ route('meter.readings.destroy', [$meter, $reading]) }}" method="POST"
                                    class="inline-block"
                                    onsubmit="return confirm('Are you sure you want to delete this reading?');">
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
        </div>
    </div>
</div>

<script>
// JavaScript to handle form submission validation for adding a reading
document.getElementById('addReadingForm').addEventListener('submit', function(event) {
    const readingValue = document.getElementById('reading_value').value;
    const readingDate = document.getElementById('reading_date').value;
    const previousReading = @json($meter->readings->sortByDesc('reading_date')->first());
    const estimatedAnnualConsumption = {{ $meter->estimated_annual_consumption }};

    // Validate the reading value against the estimated range
    if (previousReading) {
        const previousReadingValue = previousReading.reading_value;
        const previousReadingDate = new Date(previousReading.reading_date);
        const estimateDate = new Date(readingDate);
        const daysBetween = (estimateDate - previousReadingDate) / (1000 * 60 * 60 * 24);
        const dailyConsumption = estimatedAnnualConsumption / 365;
        const estimatedReading = previousReadingValue + (dailyConsumption * daysBetween);
        const minAcceptable = estimatedReading * 0.75;
        const maxAcceptable = estimatedReading * 1.25;

        if (readingValue < minAcceptable || readingValue > maxAcceptable) {
            event.preventDefault();
            alert('Reading value is out of acceptable range.');
        }
    }
});
</script>
@endsection