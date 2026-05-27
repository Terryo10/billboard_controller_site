<?php

namespace App\Filament\Resources\BookingResource\Pages;

use App\Filament\Resources\BookingResource;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewBooking extends ViewRecord
{
    protected static string $resource = BookingResource::class;

    public function infolist(Schema $infolist): Schema
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
            Section::make('Product / Advert')->schema([
                Grid::make(3)->schema([
                    TextEntry::make('advert.title')->label('Product Name')->placeholder('—'),
                    TextEntry::make('advert.original_filename')->label('Uploaded File')->placeholder('—'),
                    TextEntry::make('advert.file_type')->label('File Type')->placeholder('—'),
                    TextEntry::make('advert.status')->label('Status')->badge()
                        ->color(fn (?string $state): string => match ($state) {
                            'approved' => 'success',
                            'rejected' => 'danger',
                            'pending_review' => 'warning',
                            default => 'gray',
                        }),
                    TextEntry::make('advert.file_size')
                        ->label('File Size')
                        ->formatStateUsing(fn ($state) => $state ? number_format($state / 1048576, 2).' MB' : '—'),
                    TextEntry::make('advert.rejection_reason')->label('Rejection Reason')->placeholder('—'),
                ]),
                ImageEntry::make('advert.file_path')
                    ->label('Preview')
                    ->visible(fn ($record) => $record->advert?->file_type === 'image')
                    ->disk('public'),
                TextEntry::make('advert.file_url')
                    ->label('Video URL')
                    ->visible(fn ($record) => $record->advert?->file_type === 'video')
                    ->url(fn (?string $state) => $state)
                    ->openUrlInNewTab(),
            ]),
        ]);
    }
}
