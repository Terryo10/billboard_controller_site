<?php

namespace App\Filament\Resources\BookingResource\Pages;

use App\Filament\Resources\BookingResource;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewBooking extends ViewRecord
{
    protected static string $resource = BookingResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make('Booking Details')->schema([
                Grid::make(3)->schema([
                    TextEntry::make('id')->label('Booking ID'),
                    TextEntry::make('user.name')->label('Advertiser'),
                    TextEntry::make('user.email')->label('Email'),
                    TextEntry::make('station.name')->label('Station'),
                    TextEntry::make('station.location_name')->label('Location'),
                    TextEntry::make('status')->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'pending' => 'warning',
                            'approved' => 'success',
                            'rejected' => 'danger',
                            'cancelled' => 'gray',
                        }),
                    TextEntry::make('slot_count')->label('Slots Booked'),
                    TextEntry::make('total_price')->money('USD')->label('Total Price'),
                    TextEntry::make('paid_at')->dateTime()->label('Paid At'),
                ]),
            ]),
            Section::make('Advert')->schema([
                TextEntry::make('advert.title')->label('Title'),
                TextEntry::make('advert.status')->label('Status')->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'pending_review' => 'warning',
                        default => 'gray',
                    }),
                TextEntry::make('advert.file_type')->label('File Type'),
                TextEntry::make('rejection_reason')->label('Rejection Reason')->visible(fn ($record) => $record->rejection_reason),
            ]),
        ]);
    }
}
