<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingInvoice extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Booking $booking)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $slots = $this->booking->bookingSlots;
        $firstAir = $slots->sortBy('air_date')->first()?->air_date?->format('M j, Y');
        $lastAir = $slots->sortByDesc('air_date')->first()?->air_date?->format('M j, Y');

        return (new MailMessage)
            ->subject("Invoice for Booking #{$this->booking->id} — Billboard Controller")
            ->greeting("Hello {$notifiable->name},")
            ->line("Here is your invoice for your completed campaign on **{$this->booking->station->name}**.")
            ->line("**Booking ID:** #{$this->booking->id}")
            ->line("**Station:** {$this->booking->station->name} ({$this->booking->station->location_name})")
            ->line("**Campaign Dates:** {$firstAir} – {$lastAir}")
            ->line("**Slots Played:** {$this->booking->slot_count}")
            ->line("**Amount Paid:** \${$this->booking->total_price}")
            ->line('Thank you for advertising with Billboard Controller!')
            ->action('View My Adverts', url(route('my-adverts')));
    }
}
