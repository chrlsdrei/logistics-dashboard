<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery List - Proximity Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-zumthor-50 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold text-zumthor-900">Delivery List</h1>
                <a href="{{ route('dashboard') }}"
                   class="bg-zumthor-600 hover:bg-zumthor-700 text-white font-medium py-2 px-4 rounded-md transition duration-200">
                    Back to Dashboard
                </a>
            </div>
            <p class="text-zumthor-600 mt-2">Complete list of all delivery proximity checks</p>
        </div>

        <!-- Delivery Table -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-zumthor-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zumthor-700 uppercase tracking-wider">
                                Delivery ID
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zumthor-700 uppercase tracking-wider">
                                Time
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zumthor-700 uppercase tracking-wider">
                                Delivery Latitude
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zumthor-700 uppercase tracking-wider">
                                Delivery Longitude
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zumthor-700 uppercase tracking-wider">
                                Alert Radius (M)
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zumthor-700 uppercase tracking-wider">
                                Distance (M)
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zumthor-700 uppercase tracking-wider">
                                Status
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-zumthor-200">
                        @forelse($deliveries as $delivery)
                            <tr class="hover:bg-zumthor-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zumthor-900">
                                    {{ $delivery->id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-zumthor-900">
                                    {{ $delivery->created_at->format('Y-m-d H:i:s') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-zumthor-900">
                                    {{ number_format($delivery->delivery_lat, 6) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-zumthor-900">
                                    {{ number_format($delivery->delivery_lng, 6) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-zumthor-900">
                                    {{ $delivery->radius_meters }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-zumthor-900">
                                    {{ number_format($delivery->distance_meters, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($delivery->is_within_range)
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            Within Range
                                        </span>
                                    @else
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                            Out of Range
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-zumthor-600">
                                    No delivery records found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($deliveries->hasPages())
                <div class="px-6 py-4 border-t border-zumthor-200">
                    {{ $deliveries->links() }}
                </div>
            @endif
        </div>

        <!-- Summary Stats -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h3 class="text-lg font-semibold text-zumthor-800 mb-2">Total Deliveries</h3>
                <p class="text-3xl font-bold text-zumthor-600">{{ $deliveries->total() }}</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h3 class="text-lg font-semibold text-zumthor-800 mb-2">Within Range</h3>
                <p class="text-3xl font-bold text-green-600">{{ $deliveries->where('is_within_range', true)->count() }}</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h3 class="text-lg font-semibold text-zumthor-800 mb-2">Out of Range</h3>
                <p class="text-3xl font-bold text-red-600">{{ $deliveries->where('is_within_range', false)->count() }}</p>
            </div>
        </div>
    </div>
</body>
</html>
