<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meter Readings Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.0/flowbite.min.css" rel="stylesheet" />
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .stat-card {
            background: linear-gradient(135deg, #ffffff 0%, #f3f4f6 100%);
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="gradient-bg text-white shadow-lg">
        <div class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
            <a href="{{ route('meters.index') }}">
                <div class="flex items-center">
                    <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    <span class="ml-3 text-xl font-bold">Meter Manager</span>
                </div>
            </a>
                <div class="flex items-center space-x-4">
                    
                    <a href="{{ route('meters.create') }}" class="bg-white text-blue-900 px-4 py-2 rounded-lg hover:bg-gray-100 transition duration-300">Add Meter</a>
                    <a href="{{ route('meters.bulkUploadForm') }}" class="bg-white text-blue-900 px-4 py-2 rounded-lg hover:bg-gray-100 transition duration-300">Bulk Upload Readings</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <div class="container mx-auto px-6 py-8">
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded" role="alert">
                <p class="font-bold">Success</p>
                <p>{{ session('success') }}</p>
            </div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.0/flowbite.min.js"></script>
</body>
</html>