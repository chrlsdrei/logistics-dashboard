<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProximityAlertController;

Route::get('/', [ProximityAlertController::class, 'index'])->name('dashboard');
Route::post('/check-proximity', [ProximityAlertController::class, 'checkProximity'])->name('check.proximity');
