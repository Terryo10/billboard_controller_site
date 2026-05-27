<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StationOfflineAlert extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Collection $stations)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('⚠️ Offline Station Alert — Billboard Controller')
            ->greeting("Hello {$notifiable->name},")
            ->line("The following {$this->stations->count()} station(s) have not sent a heartbeat in over 30 minutes:");

        foreach ($this->stations as $station) {
            $lastSeen = $station->last_heartbeat_at
                ? $station->last_heartbeat_at->diffForHumans()
                : 'Never';
            $mail->line("• **{$station->name}** ({$station->location_name}) — Last seen: {$lastSeen}");
        }

        return $mail->action('View Admin Panel', url('/admin'));
    }
}
