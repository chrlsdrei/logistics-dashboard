<?php

namespace App\Http\Controllers;

use App\Models\ProximityLog; // <-- Make sure this is imported
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ProximityAlertController extends Controller
{
    /**
     * Show the main dashboard page.
     * This is the missing method.
     */
    public function index()
    {
        $logs = ProximityLog::latest()->take(10)->get();

        return view('dashboard.index', [
            'logs' => $logs,
            'data' => null
        ]);
    }

    /**
     * Check proximity, log it, and return to the dashboard with the result.
     */
    public function checkProximity(Request $request)
    {
        $validated = $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'radius' => 'sometimes|numeric',
        ]);

        $response = Http::post('https://flask-proximity-alert-htlk.onrender.com/check_proximity', [
            'warehouse' => [14.5995, 120.9842],
            'delivery' => [
                (float)$validated['lat'],
                (float)$validated['lng']
            ],
            'radius' => $validated['radius'] ?? 250
        ]);

        $data = $response->json();

        if ($data && isset($data['distance'])) {
            ProximityLog::create([
                'delivery_lat' => $validated['lat'],
                'delivery_lng' => $validated['lng'],
                'radius_meters' => $validated['radius'] ?? 250,
                'distance_meters' => $data['distance'],
                'is_within_range' => $data['within_range'],
            ]);
        }

        $logs = ProximityLog::latest()->take(10)->get();

        return redirect()->route('dashboard')
                 ->with('proximity_data', $data);
    }
}
