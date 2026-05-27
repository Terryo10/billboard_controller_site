<?php

namespace App\Providers;

use App\Models\Advert;
use App\Models\Booking;
use App\Observers\AdvertObserver;
use App\Observers\BookingObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Booking::observe(BookingObserver::class);
        Advert::observe(AdvertObserver::class);
    }
}
