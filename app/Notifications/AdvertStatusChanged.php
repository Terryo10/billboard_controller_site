<?php

namespace App\Notifications;

use App\Models\Advert;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdvertStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Advert $advert)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $approved = $this->advert->status === 'approved';

        $mail = (new MailMessage)
            ->subject($approved ? 'Advert Approved ✅' : 'Advert Rejected ❌')
            ->greeting("Hello {$notifiable->name},");

        if ($approved) {
            $mail->line("Great news! Your advert **{$this->advert->title}** has been approved.")
                 ->line('It will now be displayed according to your booked schedule.');
        } else {
            $mail->line("Your advert **{$this->advert->title}** was rejected.")
                 ->line("**Reason:** {$this->advert->rejection_reason}")
                 ->line('Please upload a revised version or contact support.');
        }

        return $mail->action('View My Adverts', url(route('my-adverts')));
    }
}
