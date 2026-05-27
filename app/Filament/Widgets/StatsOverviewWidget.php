<?php

namespace App\Filament\Widgets;

use App\Models\Advert;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Station;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $revenueThisMonth = Payment::where('status', 'completed')
            ->whereMonth('processed_at', now()->month)
            ->whereYear('processed_at', now()->year)
            ->sum('amount');

        $activeBookingsToday = Booking::where('status', 'approved')
            ->whereHas('bookingSlots', fn ($q) => $q->whereDate('air_date', today()))
            ->count();

        return [
            Stat::make('Total Stations', Station::count())
                ->description(Station::where('status', 'active')->count().' active')
                ->icon('heroicon-o-tv')
                ->color('primary'),

            Stat::make('Active Bookings Today', $activeBookingsToday)
                ->description('Playing on screens right now')
                ->icon('heroicon-o-play-circle')
                ->color('success'),

            Stat::make('Revenue This Month', '$'.number_format($revenueThisMonth, 2))
                ->description('Confirmed payments')
                ->icon('heroicon-o-banknotes')
                ->color('success'),

            Stat::make('Pending Approvals', Booking::pending()->count() + Advert::awaitingReview()->count())
                ->description(Booking::pending()->count().' bookings + '.Advert::awaitingReview()->count().' adverts')
                ->icon('heroicon-o-clock')
                ->color('warning'),
        ];
    }
}
