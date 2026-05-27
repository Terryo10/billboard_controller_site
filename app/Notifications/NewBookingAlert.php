<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewBookingAlert extends Notification implements ShouldQueue
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
        return (new MailMessage)
            ->subject('New Booking Awaiting Approval — Billboard Controller')
            ->greeting("Hello {$notifiable->name},")
            ->line("A new booking has been submitted and requires your approval.")
            ->line("**Booking ID:** #{$this->booking->id}")
            ->line("**Advertiser:** {$this->booking->user->name} ({$this->booking->user->email})")
            ->line("**Station:** {$this->booking->station->name}")
            ->line("**Slots:** {$this->booking->slot_count}")
            ->line("**Total:** \${$this->booking->total_price}")
            ->action('Review Booking', url("/admin/bookings/{$this->booking->id}"));
    }
}
