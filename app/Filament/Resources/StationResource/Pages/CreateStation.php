<?php

namespace App\Filament\Resources\StationResource\Pages;

use App\Filament\Resources\StationResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateStation extends CreateRecord
{
    protected static string $resource = StationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['device_token'] = Str::random(64);
        return $data;
    }
}
