<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingConfirmed extends Notification implements ShouldQueue
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
            ->subject('Booking Confirmed — Billboard Controller')
            ->greeting("Hello {$notifiable->name},")
            ->line("Your booking for **{$this->booking->station->name}** has been confirmed.")
            ->line("**Booking ID:** #{$this->booking->id}")
            ->line("**Total:** \${$this->booking->total_price}")
            ->line("**Slots:** {$this->booking->slot_count}")
            ->action('View My Adverts', url(route('my-adverts')))
            ->line('Your advert is under review. We will notify you once it is approved.');
    }
}
