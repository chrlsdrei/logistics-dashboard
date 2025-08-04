<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proximity Check</title>
    <style>
        body { font-family: sans-serif; max-width: 500px; margin: 2em auto; }
        form div { margin-bottom: 1em; }
        label { display: block; margin-bottom: .2em; }
        input, select, button { width: 100%; padding: .5em; box-sizing: border-box; }
        button { background-color: #3490dc; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body>
    <h1>Check Delivery Proximity</h1>

    <form method="POST" action="{{ route('check.proximity') }}">
        @csrf

        <div>
            <label for="lat">Delivery Latitude:</label>
            <input type="text" id="lat" name="lat" required>
        </div>

        <div>
            <label for="lng">Delivery Longitude:</label>
            <input type="text" id="lng" name="lng" required>
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
</body>
</html>
