<?php

namespace App\Observers;

use App\Models\Booking;
use App\Models\User;
use App\Notifications\BookingConfirmed;
use App\Notifications\NewBookingAlert;

class BookingObserver
{
    public function created(Booking $booking): void
    {
        // Notify advertiser
        $booking->user->notify(new BookingConfirmed($booking));

        // Notify all admins
        User::where('role', 'admin')->get()->each(
            fn ($admin) => $admin->notify(new NewBookingAlert($booking))
        );
    }
}
