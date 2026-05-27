<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\BookingSlot;
use App\Notifications\BookingInvoice;
use Illuminate\Console\Command;

class GenerateInvoices extends Command
{
    protected $signature = 'invoices:generate';
    protected $description = 'Generate and send invoices for bookings with completed air dates';

    public function handle(): void
    {
        // Bookings where all slots have aired and no invoice sent yet
        $bookings = Booking::where('status', 'approved')
            ->whereNotNull('paid_at')
            ->whereDoesntHave('bookingSlots', fn ($q) => $q->where('air_date', '>=', today()))
            ->with(['user', 'station', 'bookingSlots', 'advert'])
            ->get();

        $count = 0;
        foreach ($bookings as $booking) {
            $booking->user->notify(new BookingInvoice($booking));
            $count++;
        }

        $this->info("Sent {$count} invoice(s).");
    }
}
