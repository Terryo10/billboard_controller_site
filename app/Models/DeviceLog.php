<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'station_id',
        'event_type',
        'payload',
        'logged_at',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'logged_at' => 'datetime',
        ];
    }

    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }
}
