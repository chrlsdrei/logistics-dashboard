<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proximity Dashboard</title>
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

</body>
</html>
