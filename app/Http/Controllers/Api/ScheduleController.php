<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingSlot;
use App\Models\Station;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ScheduleController extends Controller
{
    /**
     * GET /api/station/{station}/schedule
     * Returns the play schedule for today + next 7 days.
     */
    public function schedule(Request $request, int $station): JsonResponse
    {
        $station = Station::findOrFail($station);
        $this->authorizeStation($request, $station);

        $from = today();
        $to = today()->addDays(7);

        $slots = BookingSlot::with(['booking.advert', 'slotTemplate'])
            ->whereBetween('air_date', [$from, $to])
            ->whereHas('booking', function ($q) use ($station) {
                $q->where('station_id', $station->id)
                  ->where('status', 'approved')
                  ->whereNotNull('paid_at')
                  ->whereHas('advert', fn ($a) => $a->where('status', 'approved'));
            })
            ->orderBy('air_date')
            ->orderBy('start_time')
            ->get();

        $grouped = $slots->groupBy(fn ($s) => $s->booking->advert->id ?? null)
            ->filter(fn ($group, $key) => $key !== null)
            ->map(function ($group) {
                $first = $group->first();
                $advert = $first->booking->advert;

                return [
                    'advert_id'        => $advert->id,
                    'title'            => $advert->title,
                    'file_url'         => Storage::url($advert->file_path),
                    'file_type'        => $advert->file_type,
                    'duration_seconds' => $advert->duration_seconds,
                    'checksum'         => $advert->checksum,
                    'scheduled_slots'  => $group->map(fn ($s) => [
                        'day'        => $s->air_date->format('Y-m-d'),
                        'start_time' => $s->start_time,
                        'end_time'   => $s->end_time,
                    ])->values(),
                ];
            })
            ->values();

        return response()->json([
            'station_id' => $station->id,
            'generated_at' => now()->toIso8601String(),
            'schedule' => $grouped,
        ]);
    }

    /**
     * GET /api/station/{station}/media
     * Returns the full media manifest with download URLs and checksums.
     */
    public function media(Request $request, int $station): JsonResponse
    {
        $station = Station::findOrFail($station);
        $this->authorizeStation($request, $station);

        $adverts = Booking::where('station_id', $station->id)
            ->where('status', 'approved')
            ->whereNotNull('paid_at')
            ->with('advert')
            ->get()
            ->pluck('advert')
            ->filter(fn ($a) => $a && $a->status === 'approved')
            ->map(fn ($advert) => [
                'advert_id'        => $advert->id,
                'title'            => $advert->title,
                'file_url'         => Storage::url($advert->file_path),
                'file_type'        => $advert->file_type,
                'file_size'        => $advert->file_size,
                'checksum'         => $advert->checksum,
                'duration_seconds' => $advert->duration_seconds,
            ])
            ->values();

        return response()->json([
            'station_id'   => $station->id,
            'generated_at' => now()->toIso8601String(),
            'media'        => $adverts,
        ]);
    }

    private function authorizeStation(Request $request, Station $station): void
    {
        $authenticatedStation = $request->attributes->get('station');
        abort_if($authenticatedStation->id !== $station->id, 403, 'Access denied');
    }
}
