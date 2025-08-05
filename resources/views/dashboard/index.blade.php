<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proximity Dashboard</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <style>
        body { font-family: sans-serif; max-width: 600px; margin: 2em auto; }
        .container { border: 1px solid #eee; padding: 2em; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        form div { margin-bottom: 1em; text-align: left; }
        label { display: block; margin-bottom: .3em; font-weight: bold; }
        input, select, button { width: 100%; padding: .8em; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        button { background-color: #3490dc; color: white; border: none; cursor: pointer; font-size: 1em; }
        button:hover { background-color: #2779bd; }

        .alert { padding: 1em; margin-top: 1.5em; border-radius: 5px; color: white; font-size: 1.1em; text-align: center; }
        .alert-success { background-color: #28a745; }
        .alert-danger { background-color: #dc3545; }

        table { width: 100%; border-collapse: collapse; margin-top: 1em; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .status-in { color: #28a745; font-weight: bold; }
        .status-out { color: #dc3545; font-weight: bold; }

        hr { margin-top: 2.5em; margin-bottom: 2em; border: 0; border-top: 1px solid #eee; }
        h1, h2 { text-align: center; }
    </style>
</head>
<body>

    <div class="container">
        <h1>Proximity Dashboard</h1>

        @if(session('proximity_data'))
            @if(isset(session('proximity_data')['within_range']))
                @if (session('proximity_data')['within_range'])
                    <p class="alert alert-success">
                        <strong>Success!</strong> Delivery is within <strong>{{ session('proximity_data')['distance'] }}</strong> meters.
                    </p>
                @else
                    <p class="alert alert-danger">
                        <strong>Alert!</strong> Delivery is <strong>{{ session('proximity_data')['distance'] }}</strong> meters away.
                    </p>
                @endif
            @else
                <p class="alert alert-danger">
                    An error occurred. Could not retrieve data from the API.
                </p>
            @endif
        @endif

        <h2>Check New Delivery</h2>
        <form method="POST" action="{{ route('check.proximity') }}">
            @csrf

            <div>
                <label for="lat">Delivery Latitude:</label>
                <input type="text" id="lat" name="lat" value="{{ old('lat') }}" required>
            </div>

            <div>
                <label for="lng">Delivery Longitude:</label>
                <input type="text" id="lng" name="lng" value="{{ old('lng') }}" required>
            </div>

            <div>
                <label for="radius">Alert Radius (m):</label>
                <select id="radius" name="radius">
                    <option value="100">100m</option>
                    <option value="250" selected>250m</option>
                    <option value="500">500m</option>
                </select>
            </div>

            <button type="submit">Check Proximity</button>
        </form>

        <hr>
        <div id="map" style="height: 400px; width: 100%; margin-bottom: 2em; border-radius: 8px;"></div>
        <h2>Recent Proximity Checks</h2>
        @if($logs->isEmpty())
            <p style="text-align: center;">No proximity checks have been logged yet.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Delivery ID</th>
                        <th>Time</th>
                        <th>Distance (m)</th>
                        <th>Alert Radius (m)</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                        <tr>
                            <td>{{ $log->id }}</td>
                            <td>{{ $log->created_at->format('Y-m-d H:i') }}</td>
                            <td>{{ $log->distance_meters }}</td>
                            <td>{{ $log->radius_meters }}</td>
                            <td>
                                @if($log->is_within_range)
                                    <span class="status-in">Within Range</span>
                                @else
                                    <span class="status-out">Out of Range</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <script>
        // --- 1. Define Coordinates (using data from PHP) ---

        // Get warehouse coordinates. We check the session first for after a redirect,
        // otherwise we use the variable passed on initial page load.
        const warehouseCoords = [
            {{ session('warehouse_coords_for_map.0') ?? $warehouseCoords[0] }},
            {{ session('warehouse_coords_for_map.1') ?? $warehouseCoords[1] }}
        ];

        // Get the last delivery coordinates from the session data, if it exists
        // Otherwise, default to the warehouse location so the map doesn't break
        const deliveryCoords = [
            {{ session('proximity_data.delivery.0') ?? $warehouseCoords[0] }},
            {{ session('proximity_data.delivery.1') ?? $warehouseCoords[1] }}
        ];

        // --- 2. Initialize the Map ---
        const map = L.map('map').setView(warehouseCoords, 15);

        // --- 3. Add the Tile Layer ---
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);

        // --- 4. Add Markers ---
        const warehouseMarker = L.marker(warehouseCoords).addTo(map)
            .bindPopup('<b>Warehouse Location</b>');

        // Only add the delivery marker if a check has been performed
        @if(session('proximity_data'))
            const deliveryMarker = L.marker(deliveryCoords).addTo(map)
                .bindPopup('<b>Last Delivery Location</b>');

            // --- 5. Fit Map to Markers ---
            const group = new L.featureGroup([warehouseMarker, deliveryMarker]);
            map.fitBounds(group.getBounds().pad(0.5));
        @endif
    </script>

</body>
</html>
