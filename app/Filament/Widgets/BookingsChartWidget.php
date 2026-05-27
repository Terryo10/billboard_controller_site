<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class BookingsChartWidget extends ChartWidget
{
    protected ?string $heading = 'Bookings (Last 30 Days)';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = collect(range(29, 0))->map(function (int $daysAgo) {
            $date = now()->subDays($daysAgo);
            return [
                'label' => $date->format('M j'),
                'count' => Booking::whereDate('created_at', $date)->count(),
            ];
        });

        return [
            'datasets' => [
                [
                    'label' => 'Bookings',
                    'data' => $data->pluck('count')->toArray(),
                    'backgroundColor' => 'rgba(59, 130, 246, 0.5)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'fill' => true,
                ],
            ],
            'labels' => $data->pluck('label')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
