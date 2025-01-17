@extends('layouts.app')

@section('content')
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="p-6 border-b">
        <!-- Page header -->
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Meters Overview</h2>
        <!-- Search form -->
        <form id="searchForm" action="{{ route('meters.index') }}" method="GET"
            class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <!-- Search input -->
                <input type="text" name="search" id="searchInput" placeholder="Search by MPXN"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    value="{{ request('search') }}">
            </div>
        </form>
    </div>
    <div class="overflow-x-auto">
        <!-- Meters table -->
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">MPXN</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Type
                        <!-- Type filter dropdown -->
                        <select name="type" id="typeFilterHeader"
                            class="shadow appearance-none border rounded py-1 px-2 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="">All</option>
                            <option value="electricity" {{ request('type') == 'electricity' ? 'selected' : '' }}>
                                Electricity</option>
                            <option value="gas" {{ request('type') == 'gas' ? 'selected' : '' }}>Gas</option>
                        </select>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <!-- Sort link for installation date -->
                        <a href="#" id="sortLinkHeader">
                            Installation Date
                            @if(request('sort') == 'asc')
                            &uarr;
                            @else
                            &darr;
                            @endif
                        </a>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last
                        Reading</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">View</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Delete
                    </th>
                </tr>
            </thead>
            <tbody id="meterTableBody" class="bg-white divide-y divide-gray-200">
                @foreach($meters as $meter)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $meter->mpxn }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span
                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $meter->type === 'electricity' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                            {{ ucfirst($meter->type) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ \Carbon\Carbon::parse($meter->installation_date)->format('M d, Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $meter->readings->sortByDesc('reading_date')->first()?->reading_value ?? 'No readings' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('meters.show', $meter) }}"
                            class="text-blue-600 hover:text-blue-900 bg-blue-50 px-3 py-1 rounded-full">View Details</a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <form action="{{ route('meters.destroy', $meter) }}" method="POST" class="inline-block"
                            onsubmit="return confirm('Are you sure you want to delete this meter?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
// Event listener for search input
document.getElementById('searchInput').addEventListener('input', function() {
    fetchMeters();
});

// Event listener for type filter dropdown
document.getElementById('typeFilterHeader').addEventListener('change', function() {
    fetchMeters();
});

// Event listener for sort link
document.getElementById('sortLinkHeader').addEventListener('click', function(event) {
    event.preventDefault();
    toggleSort();
    fetchMeters();
});

// Function to toggle sort order
function toggleSort() {
    const urlParams = new URLSearchParams(window.location.search);
    const sort = urlParams.get('sort');
    urlParams.set('sort', sort === 'asc' ? 'desc' : 'asc');
    history.replaceState(null, '', '?' + urlParams.toString());
}

// Function to fetch meters based on search, type, and sort
function fetchMeters() {
    const search = document.getElementById('searchInput').value;
    const type = document.getElementById('typeFilterHeader').value;
    const urlParams = new URLSearchParams(window.location.search);
    const sort = urlParams.get('sort') || 'asc';

    fetch(`{{ route('meters.index') }}?search=${search}&type=${type}&sort=${sort}`)
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newTableBody = doc.getElementById('meterTableBody').innerHTML;
            document.getElementById('meterTableBody').innerHTML = newTableBody;
        });
}
</script>
@endsection