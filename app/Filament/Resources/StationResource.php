<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StationResource\Pages;
use App\Models\Station;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class StationResource extends Resource
{
    protected static ?string $model = Station::class;
    protected static ?string $navigationIcon = 'heroicon-o-tv';
    protected static ?string $navigationGroup = 'Infrastructure';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Station Details')->schema([
                Grid::make(2)->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('location_name')
                        ->required()
                        ->maxLength(255)
                        ->label('Location'),
                ]),
                Textarea::make('description')
                    ->rows(3)
                    ->columnSpanFull(),
                FileUpload::make('photo')
                    ->image()
                    ->directory('stations')
                    ->columnSpanFull(),
            ]),
            Section::make('GPS Coordinates')->schema([
                Grid::make(2)->schema([
                    TextInput::make('lat')
                        ->numeric()
                        ->label('Latitude'),
                    TextInput::make('lng')
                        ->numeric()
                        ->label('Longitude'),
                ]),
            ]),
            Section::make('Screen Specifications')->schema([
                Grid::make(3)->schema([
                    TextInput::make('screen_size')
                        ->label('Screen Size')
                        ->placeholder('e.g. 55 inch'),
                    TextInput::make('screen_width')
                        ->numeric()
                        ->label('Width (px)'),
                    TextInput::make('screen_height')
                        ->numeric()
                        ->label('Height (px)'),
                ]),
            ]),
            Section::make('Status & Device')->schema([
                Grid::make(2)->schema([
                    Select::make('status')
                        ->options([
                            'active' => 'Active',
                            'inactive' => 'Inactive',
                        ])
                        ->required(),
                    TextInput::make('device_token')
                        ->label('Device Token')
                        ->disabled()
                        ->dehydrated(false),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo')
                    ->circular()
                    ->defaultImageUrl(asset('images/station-placeholder.png')),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('location_name')
                    ->label('Location')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'gray',
                    }),
                IconColumn::make('is_online')
                    ->label('Online')
                    ->boolean()
                    ->getStateUsing(fn (Station $record): bool => $record->isOnline()),
                TextColumn::make('last_heartbeat_at')
                    ->label('Last Seen')
                    ->dateTime()
                    ->since()
                    ->sortable(),
                TextColumn::make('bookings_count')
                    ->counts('bookings')
                    ->label('Bookings'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ]),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStations::route('/'),
            'create' => Pages\CreateStation::route('/create'),
            'view' => Pages\ViewStation::route('/{record}'),
            'edit' => Pages\EditStation::route('/{record}/edit'),
        ];
    }
}
