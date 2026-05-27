<?php

use App\Http\Controllers\Api\DeviceController;
use App\Http\Controllers\Api\ScheduleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — Flutter Device API
|--------------------------------------------------------------------------
| Authenticated with a per-station API key sent as:
|   Authorization: Bearer {device_token}
|   or X-Device-Token: {device_token}
*/

Route::prefix('station')->group(function () {
    // Device registration (public — no auth required)
    Route::post('/register', [DeviceController::class, 'register'])->name('api.station.register');

    // Authenticated device routes
    Route::middleware('device.auth')->group(function () {
        Route::get('/{station}/schedule', [ScheduleController::class, 'schedule'])->name('api.station.schedule');
        Route::get('/{station}/media', [ScheduleController::class, 'media'])->name('api.station.media');
        Route::post('/{station}/heartbeat', [DeviceController::class, 'heartbeat'])->name('api.station.heartbeat');
    });
});
