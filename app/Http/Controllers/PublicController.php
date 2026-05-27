<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Station;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PublicController extends Controller
{
    public function home()
    {
        $stations = Station::active()
            ->with('timeSlotTemplates')
            ->orderBy('name')
            ->get();

        $mapStations = $stations
            ->filter(fn ($s) => $s->lat && $s->lng)
            ->map(fn ($s) => [
                'name'     => $s->name,
                'location' => $s->location_name,
                'lat'      => $s->lat,
                'lng'      => $s->lng,
                'url'      => route('stations.show', $s),
            ])
            ->values();

        return view('pages.home', compact('stations', 'mapStations'));
    }

    public function stationsIndex()
    {
        $stations = Station::active()
            ->with('timeSlotTemplates')
            ->orderBy('name')
            ->paginate(12);

        return view('pages.stations-index', compact('stations'));
    }

    public function stationShow(Station $station)
    {
        $station->load('timeSlotTemplates');
        return view('pages.station-show', compact('station'));
    }

    public function myAdverts()
    {
        $bookings = Booking::with(['station', 'advert', 'bookingSlots'])
            ->where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('pages.my-adverts', compact('bookings'));
    }

    public function cancelBooking(Request $request, Booking $booking)
    {
        abort_if($booking->user_id !== Auth::id(), 403);
        abort_if(! in_array($booking->status, ['pending', 'approved']), 422, 'Cannot cancel this booking.');

        $booking->update(['status' => 'cancelled']);

        return back()->with('success', 'Booking cancelled.');
    }
}
