<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proximity Alert</title>
     <style>
        body { font-family: sans-serif; max-width: 500px; margin: 2em auto; text-align: center; }
        .alert { padding: 1em; border-radius: 5px; color: white; font-size: 1.2em; }
        .alert-success { background-color: #28a745; }
        .alert-danger { background-color: #dc3545; }
        a { display: inline-block; margin-top: 1em; }
    </style>
</head>
<body>
    <h1>Proximity Alert Result</h1>

    @if(isset($data) && isset($data['within_range']))
        @if ($data['within_range'])
            <p class="alert alert-success">
                <strong>Success!</strong> Delivery is within <strong>{{ $data['distance'] }}</strong> meters.
            </p>
        @else
            <p class="alert alert-danger">
                <strong>Alert!</strong> Delivery is <strong>{{ $data['distance'] }}</strong> meters away.
            </p>
        @endif
    @else
        <p class="alert alert-danger">
            An error occurred. Could not retrieve data from the API.
        </p>
    @endif

    <a href="{{ route('proximity.form') }}">Check Another</a>
</body>
</html>
