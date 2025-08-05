<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proximity Dashboard</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-zumthor-50 min-h-screen">
    <div class="flex h-screen">
        <!-- Map Section (flexible width - takes remaining space) -->
        <div class="flex-1 h-full">
            <div id="map" class="w-full h-full"></div>
        </div>

        <!-- Sidebar Section (fixed width to accommodate table) -->
        <div class="w-96 h-full bg-white shadow-lg overflow-y-auto">
            <div class="p-6">
                <h1 class="text-2xl font-bold text-zumthor-900 mb-6 text-center">Proximity Dashboard</h1>

                @if(session('proximity_data'))
                    @if(isset(session('proximity_data')['within_range']))
                        @if (session('proximity_data')['within_range'])
                            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                                <strong>Success!</strong> Delivery is within <strong>{{ session('proximity_data')['distance'] }}</strong> meters.
                            </div>
                        @else
                            <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                                <strong>Alert!</strong> Delivery is <strong>{{ session('proximity_data')['distance'] }}</strong> meters away.
                            </div>
                        @endif
                    @else
                        <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                            An error occurred. Could not retrieve data from the API.
                        </div>
                    @endif
                @endif

                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-zumthor-800 mb-4">Check New Delivery</h2>
                    <form method="POST" action="{{ route('check.proximity') }}" class="space-y-4">
                        @csrf

                        <div>
                            <label for="lat" class="block text-sm font-medium text-zumthor-700 mb-2">Delivery Latitude:</label>
                            <input type="text" id="lat" name="lat" value="{{ old('lat') }}" required
                                   class="w-full px-3 py-2 border border-zumthor-300 rounded-md focus:outline-none focus:ring-2 focus:ring-zumthor-500 focus:border-zumthor-500">
                        </div>

                        <div>
                            <label for="lng" class="block text-sm font-medium text-zumthor-700 mb-2">Delivery Longitude:</label>
                            <input type="text" id="lng" name="lng" value="{{ old('lng') }}" required
                                   class="w-full px-3 py-2 border border-zumthor-300 rounded-md focus:outline-none focus:ring-2 focus:ring-zumthor-500 focus:border-zumthor-500">
                        </div>

                        <div>
                            <label for="radius" class="block text-sm font-medium text-zumthor-700 mb-2">Alert Radius (m):</label>
                            <select id="radius" name="radius"
                                    class="w-full px-3 py-2 border border-zumthor-300 rounded-md focus:outline-none focus:ring-2 focus:ring-zumthor-500 focus:border-zumthor-500">
                                <option value="100">100m</option>
                                <option value="250" selected>250m</option>
                                <option value="500">500m</option>
                            </select>
                        </div>

                        <button type="submit"
                                class="w-full bg-zumthor-600 hover:bg-zumthor-700 text-white font-medium py-2 px-4 rounded-md transition duration-200">
                            Check Proximity
                        </button>
                    </form>
                </div>

                <div>
                    <h2 class="text-xl font-semibold text-zumthor-800 mb-4">Recent Proximity Checks</h2>
                    @if($logs->isEmpty())
                        <p class="text-center text-zumthor-600 py-4">No proximity checks have been logged yet.</p>
                    @else
                        <div class="overflow-hidden">
                            <table class="w-full bg-white border border-zumthor-200 rounded-lg overflow-hidden text-xs">
                                <thead class="bg-zumthor-100">
                                    <tr>
                                        <th class="px-2 py-2 text-left font-medium text-zumthor-700 uppercase tracking-wider">ID</th>
                                        <th class="px-2 py-2 text-left font-medium text-zumthor-700 uppercase tracking-wider">Time</th>
                                        <th class="px-2 py-2 text-left font-medium text-zumthor-700 uppercase tracking-wider">Dist (m)</th>
                                        <th class="px-2 py-2 text-left font-medium text-zumthor-700 uppercase tracking-wider">Radius</th>
                                        <th class="px-2 py-2 text-left font-medium text-zumthor-700 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zumthor-200">
                                    @foreach($logs as $log)
                                        <tr class="hover:bg-zumthor-50">
                                            <td class="px-2 py-2 text-zumthor-900">{{ $log->id }}</td>
                                            <td class="px-2 py-2 text-zumthor-900">{{ $log->created_at->format('m-d H:i') }}</td>
                                            <td class="px-2 py-2 text-zumthor-900">{{ $log->distance_meters }}</td>
                                            <td class="px-2 py-2 text-zumthor-900">{{ $log->radius_meters }}</td>
                                            <td class="px-2 py-2">
                                                @if($log->is_within_range)
                                                    <span class="inline-flex px-1 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                        In Range
                                                    </span>
                                                @else
                                                    <span class="inline-flex px-1 py-0.5 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                        Out of Range
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Data attributes to pass coordinates to JavaScript -->
    <div id="map-data"
         data-warehouse-lat="{{ session('warehouse_coords_for_map.0') ?? $warehouseCoords[0] }}"
         data-warehouse-lng="{{ session('warehouse_coords_for_map.1') ?? $warehouseCoords[1] }}"
         data-delivery-lat="{{ session('proximity_data.delivery.0') ?? $warehouseCoords[0] }}"
         data-delivery-lng="{{ session('proximity_data.delivery.1') ?? $warehouseCoords[1] }}"
         data-radius="{{ session('proximity_data.radius') ?? 0 }}">
    </div>
</body>
</html>
