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
<div class="mb-4">
    <!-- Page header -->
    <h1 class="text-2xl font-bold mb-4">Add New Meter</h1>
</div>

<div class="bg-white shadow-md rounded-lg p-8 mb-4">
    <!-- Form for adding a new meter -->
    <form action="{{ route('meters.store') }}" method="POST" id="meterForm">
        @csrf
        <div class="mb-4">
            <!-- Label for MPXN input -->
            <label class="block text-gray-700 text-sm font-bold mb-2" for="mpxn">
                MPXN
            </label>
            <!-- MPXN input field -->
            <input
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('mpxn') border-red-500 @enderror"
                type="text" name="mpxn" id="mpxn" value="{{ old('mpxn') }}" required>
            <!-- Error message for MPXN input -->
            @error('mpxn')
            <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
            <!-- MPXN type hint -->
            <p id="mpxnType" class="text-sm text-gray-500 mt-2"></p>
        </div>

        <div class="mb-4">
            <!-- Label for Type select -->
            <label class="block text-gray-700 text-sm font-bold mb-2" for="type">
                Type
            </label>
            <!-- Type select field -->
            <select
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('type') border-red-500 @enderror"
                name="type" id="type" required>
                <option value="">Select Type</option>
                <option value="electricity" {{ old('type') == 'electricity' ? 'selected' : '' }}>
                    Electricity
                </option>
                <option value="gas" {{ old('type') == 'gas' ? 'selected' : '' }}>
                    Gas
                </option>
            </select>
            <!-- Error message for Type select -->
            @error('type')
            <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <!-- Label for Installation Date input -->
            <label class="block text-gray-700 text-sm font-bold mb-2" for="installation_date">
                Installation Date
            </label>
            <!-- Installation Date input field -->
            <input
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('installation_date') border-red-500 @enderror"
                type="date" name="installation_date" id="installation_date" value="{{ old('installation_date') }}"
                required>
            <!-- Error message for Installation Date input -->
            @error('installation_date')
            <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <!-- Label for Estimated Annual Consumption input -->
            <label class="block text-gray-700 text-sm font-bold mb-2" for="estimated_annual_consumption">
                Estimated Annual Consumption (kWh)
            </label>
            <!-- Estimated Annual Consumption input field -->
            <input
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('estimated_annual_consumption') border-red-500 @enderror"
                type="number" name="estimated_annual_consumption" id="estimated_annual_consumption"
                value="{{ old('estimated_annual_consumption') }}" min="2000" max="8000" required>
            <!-- Error message for Estimated Annual Consumption input -->
            @error('estimated_annual_consumption')
            <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between">
            <!-- Submit button for adding the meter -->
            <button
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                type="submit">
                Add Meter
            </button>
            <!-- Cancel link -->
            <a href="{{ route('meters.index') }}" class="text-blue-500 hover:text-blue-700">
                Cancel
            </a>
        </div>
    </form>
</div>

<script>
// JavaScript to handle MPXN input and type selection
document.getElementById('mpxn').addEventListener('input', function() {
    const mpxn = this.value;
    const mpxnTypeElement = document.getElementById('mpxnType');
    const typeSelect = document.getElementById('type');
    // Check if MPXN matches MPAN (Electricity) format
    if (/^S\d{21}$/.test(mpxn)) {
        mpxnTypeElement.textContent = 'MPAN (Electricity)';
        typeSelect.value = 'electricity';
    // Check if MPXN matches MPRN (Gas) format
    } else if (/^\d{6,10}$/.test(mpxn)) {
        mpxnTypeElement.textContent = 'MPRN (Gas)';
        typeSelect.value = 'gas';
    } else {
        mpxnTypeElement.textContent = '';
        typeSelect.value = '';
    }
});

// JavaScript to handle form submission validation
document.getElementById('meterForm').addEventListener('submit', function(event) {
    const mpxn = document.getElementById('mpxn').value;
    const type = document.getElementById('type').value;
    const mpxnTypeElement = document.getElementById('mpxnType');
    const estimatedAnnualConsumption = document.getElementById('estimated_annual_consumption').value;

    // Validate MPXN and type selection
    if (/^S\d{21}$/.test(mpxn) && type !== 'electricity') {
        event.preventDefault();
        mpxnTypeElement.textContent = 'MPXN is an MPAN (Electricity), please select Electricity as the type';
        mpxnTypeElement.classList.add('text-red-500');
    } else if (/^\d{6,10}$/.test(mpxn) && type !== 'gas') {
        event.preventDefault();
        mpxnTypeElement.textContent = 'MPXN is an MPRN (Gas), please select Gas as the type';
        mpxnTypeElement.classList.add('text-red-500');
    } else {
        mpxnTypeElement.classList.remove('text-red-500');
    }

    // Validate Estimated Annual Consumption
    if (estimatedAnnualConsumption < 2000 || estimatedAnnualConsumption > 8000) {
        event.preventDefault();
        alert('Estimated Annual Consumption must be between 2,000 and 8,000 kWh.');
    }
});
</script>
@endsection