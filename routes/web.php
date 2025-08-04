<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProximityAlertController; // Import the controller

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Route to display the proximity check form
Route::get('/', [ProximityAlertController::class, 'showForm'])->name('proximity.form');
// Note: The document uses a function directly in the route, but calling a controller method is better practice. Let's add that method.

// Route to handle the form submission
Route::post('/check-proximity', [ProximityAlertController::class, 'checkProximity'])->name('check.proximity');
