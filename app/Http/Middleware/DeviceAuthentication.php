<?php

namespace App\Http\Middleware;

use App\Models\Station;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DeviceAuthentication
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken()
            ?? $request->header('X-Device-Token');

        if (! $token) {
            return response()->json(['error' => 'Device token required'], 401);
        }

        $station = Station::where('device_token', $token)->first();

        if (! $station) {
            return response()->json(['error' => 'Invalid device token'], 401);
        }

        $request->attributes->set('station', $station);

        return $next($request);
    }
}
