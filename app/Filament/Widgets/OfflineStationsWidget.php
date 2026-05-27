<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\StationResource;
use App\Models\Station;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class OfflineStationsWidget extends BaseWidget
{
    protected static ?string $heading = 'Offline Stations (No Heartbeat > 30 min)';
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Station::where('status', 'active')
                    ->where(function ($q) {
                        $q->whereNull('last_heartbeat_at')
                          ->orWhere('last_heartbeat_at', '<', now()->subMinutes(30));
                    })
            )
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('location_name')->label('Location'),
                TextColumn::make('last_heartbeat_at')
                    ->label('Last Seen')
                    ->since()
                    ->placeholder('Never'),
            ])
            ->actions([
                Action::make('view')
                    ->url(fn (Station $record) => StationResource::getUrl('view', ['record' => $record]))
                    ->icon('heroicon-o-eye'),
            ]);
    }
}
