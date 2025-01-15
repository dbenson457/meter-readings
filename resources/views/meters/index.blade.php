@extends('layouts.app')

@section('content')
    <div class="mb-4">
        <h1 class="text-2xl font-bold mb-4">Meters</h1>
        <a href="{{ route('meters.create') }}" 
           class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Add New Meter
        </a>
    </div>

    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <table class="min-w-full">
            <thead>
                <tr>
                    <th class="text-left">MPXN</th>
                    <th class="text-left">Type</th>
                    <th class="text-left">Installation Date</th>
                    <th class="text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($meters as $meter)
                    <tr>
                        <td class="py-2">{{ $meter->mpxn }}</td>
                        <td>{{ ucfirst($meter->type) }}</td>
                        <td>{{ $meter->installation_date }}</td>
                        <td>
                            <a href="{{ route('meters.show', $meter) }}" 
                               class="text-blue-500 hover:text-blue-700">
                                View Details
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection