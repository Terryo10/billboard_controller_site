<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\AdvertResource;
use App\Models\Advert;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class AdvertsAwaitingReviewWidget extends BaseWidget
{
    protected static ?string $heading = 'Adverts Awaiting Review';
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(Advert::awaitingReview()->with(['booking.user', 'booking.station']))
            ->columns([
                TextColumn::make('title'),
                TextColumn::make('booking.user.name')->label('Advertiser'),
                TextColumn::make('booking.station.name')->label('Station'),
                TextColumn::make('file_type')->badge(),
                TextColumn::make('created_at')->dateTime()->since(),
            ])
            ->actions([
                Action::make('review')
                    ->url(fn (Advert $record) => AdvertResource::getUrl('view', ['record' => $record]))
                    ->icon('heroicon-o-eye'),
            ]);
    }
}
