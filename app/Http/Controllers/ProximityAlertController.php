<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http; // Make sure this is included

class ProximityAlertController extends Controller
{
    /**
     * Check the proximity of a delivery to the warehouse.
     */
    public function checkProximity(Request $request)
{
    $request->validate([
        'lat' => 'required|numeric',
        'lng' => 'required|numeric',
        'radius' => 'sometimes|numeric',
    ]);

    $response = Http::post('https://flask-proximity-alert-htlk.onrender.com/check_proximity', [
        'warehouse' => [14.5995, 120.9842],
        'delivery' => [
            (float)$request->lat,
            (float)$request->lng
        ],
        'radius' => $request->radius ?? 250
    ]);

    return view('dashboard.alerts', ['data' => $response->json()]);
}

    public function showForm()
    {
        return view('dashboard.form');
    }
}
