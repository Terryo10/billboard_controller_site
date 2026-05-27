<?php

namespace App\Filament\Resources\AdvertResource\Pages;

use App\Filament\Resources\AdvertResource;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewAdvert extends ViewRecord
{
    protected static string $resource = AdvertResource::class;

    public function infolist(Schema $infolist): Schema
    {
        return $infolist->schema([
            Section::make('Advert Details')->schema([
                Grid::make(3)->schema([
                    TextEntry::make('title'),
                    TextEntry::make('file_type')->badge(),
                    TextEntry::make('status')->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'approved' => 'success',
                            'rejected' => 'danger',
                            'pending_review' => 'warning',
                            default => 'gray',
                        }),
                    TextEntry::make('original_filename')->label('Original File'),
                    TextEntry::make('file_size')
                        ->label('File Size')
                        ->formatStateUsing(fn ($state) => $state ? number_format($state / 1048576, 2).' MB' : '—'),
                    TextEntry::make('duration_seconds')->label('Duration (s)'),
                    TextEntry::make('checksum')->label('SHA-256')->copyable(),
                    TextEntry::make('approved_at')->dateTime(),
                    TextEntry::make('rejection_reason'),
                ]),
            ]),
            Section::make('Media Preview')->schema([
                ImageEntry::make('file_path')
                    ->label('Preview')
                    ->visible(fn ($record) => $record->file_type === 'image')
                    ->disk('public'),
                TextEntry::make('file_url')
                    ->label('Video URL')
                    ->visible(fn ($record) => $record->file_type === 'video')
                    ->url(fn ($record) => $record->file_url)
                    ->openUrlInNewTab(),
            ]),
        ]);
    }
}
