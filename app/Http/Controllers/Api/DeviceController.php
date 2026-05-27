<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeviceLog;
use App\Models\Station;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DeviceController extends Controller
{
    /**
     * POST /api/station/register
     * First-time device registration. Returns station_id + api_key (device_token).
     */
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'station_name' => 'required|string|max:255',
            'location'     => 'nullable|string|max:255',
        ]);

        $token = Str::random(64);

        $station = Station::create([
            'name'          => $request->station_name,
            'location_name' => $request->location ?? 'Unknown',
            'device_token'  => $token,
            'status'        => 'inactive', // Admin activates it
        ]);

        return response()->json([
            'station_id' => $station->id,
            'api_key'    => $token,
            'message'    => 'Station registered. An admin must activate it before it goes live.',
        ], 201);
    }

    /**
     * POST /api/station/{station}/heartbeat
     * Receives periodic heartbeat from device, updates last_heartbeat_at and logs.
     */
    public function heartbeat(Request $request, int $stationId): JsonResponse
    {
        $station = $request->attributes->get('station');

        if ($station->id !== $stationId) {
            return response()->json(['error' => 'Station mismatch'], 403);
        }

        $request->validate([
            'current_advert_id'  => 'nullable|integer',
            'uptime_seconds'     => 'nullable|integer',
            'storage_free_bytes' => 'nullable|integer',
            'errors'             => 'nullable|array',
        ]);

        $station->update(['last_heartbeat_at' => now()]);

        DeviceLog::create([
            'station_id' => $station->id,
            'event_type' => 'heartbeat',
            'payload'    => [
                'current_advert_id'  => $request->current_advert_id,
                'uptime_seconds'     => $request->uptime_seconds,
                'storage_free_bytes' => $request->storage_free_bytes,
                'errors'             => $request->errors ?? [],
                'ip'                 => $request->ip(),
            ],
            'logged_at' => now(),
        ]);

        return response()->json([
            'status'    => 'ok',
            'server_time' => now()->toIso8601String(),
        ]);
    }
}
