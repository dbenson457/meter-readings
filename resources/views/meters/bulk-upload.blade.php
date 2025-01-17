@extends('layouts.app')

@section('content')
<div class="mb-6">
    <a href="{{ route('meters.index') }}" class="inline-flex items-center text-gray-500 hover:text-gray-700">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Back to Meters
    </a>
</div>

<div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-xl font-semibold text-gray-800 mb-4">Bulk Upload Readings</h2>
    <form action="{{ route('meters.bulkUpload') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700" for="csv_file">
                Upload CSV File
            </label>
            <input type="file" name="csv_file" id="csv_file" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" required>
            @error('csv_file')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-300">
            Upload Readings
        </button>
    </form>
</div>

@if ($invalidLines)
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mt-6" role="alert">
        <strong class="font-bold">Invalid Readings:</strong>
        <pre class="block sm:inline">{{ $invalidLines }}</pre>
    </div>
@endif
@endsection