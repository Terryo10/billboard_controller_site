<?php

namespace App\Console\Commands;

use App\Models\Station;
use App\Notifications\StationOfflineAlert;
use App\Models\User;
use Illuminate\Console\Command;

class CheckOfflineStations extends Command
{
    protected $signature = 'stations:check-offline';
    protected $description = 'Check for stations with no heartbeat > 30 minutes and alert admins';

    public function handle(): void
    {
        $offlineStations = Station::where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('last_heartbeat_at')
                  ->orWhere('last_heartbeat_at', '<', now()->subMinutes(30));
            })
            ->get();

        if ($offlineStations->isEmpty()) {
            $this->info('All stations are online.');
            return;
        }

        $admins = User::where('role', 'admin')->get();

        foreach ($admins as $admin) {
            $admin->notify(new StationOfflineAlert($offlineStations));
        }

        $this->info("Alerted admins about {$offlineStations->count()} offline station(s).");
    }
}
