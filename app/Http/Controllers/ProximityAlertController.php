<?php

namespace App\Http\Controllers;

use App\Models\ProximityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ProximityAlertController extends Controller
{
    // Warehouse coordinates
    private $warehouseCoords = [14.5995, 120.9842];

    /**
     * Show the main dashboard page.
     */
    public function index()
    {
        $logs = ProximityLog::latest()->take(10)->get();

        return view('dashboard.index', [
            'logs' => $logs,
            'data' => null,
            'warehouseCoords' => $this->warehouseCoords
        ]);
    }

    public function deliveries()
    {
        $deliveries = ProximityLog::latest()->paginate(20);

        return view('dashboard.deliveries', [
            'deliveries' => $deliveries,
            'warehouseCoords' => $this->warehouseCoords
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

        if ($data) {
            $data['delivery'] = [
                (float)$validated['lat'],
                (float)$validated['lng']
            ];

            $data['radius'] = $validated['radius'] ?? 250;
        }

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
                 ->with('proximity_data', $data)
                 ->with('warehouseCoords', $this->warehouseCoords);
    }
}
