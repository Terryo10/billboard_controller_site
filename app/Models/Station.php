<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Station extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location_name',
        'lat',
        'lng',
        'photo',
        'description',
        'screen_width',
        'screen_height',
        'screen_size',
        'status',
        'device_token',
        'last_heartbeat_at',
    ];

    protected function casts(): array
    {
        return [
            'lat' => 'float',
            'lng' => 'float',
            'last_heartbeat_at' => 'datetime',
        ];
    }

    public function timeSlotTemplates(): HasMany
    {
        return $this->hasMany(TimeSlotTemplate::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function deviceLogs(): HasMany
    {
        return $this->hasMany(DeviceLog::class);
    }

    public function isOnline(): bool
    {
        if (! $this->last_heartbeat_at) {
            return false;
        }

        return $this->last_heartbeat_at->gt(now()->subMinutes(30));
    }

    public function getPhotoUrlAttribute(): ?string
    {
        if (! $this->photo) {
            return null;
        }

        return str_starts_with($this->photo, 'http')
            ? $this->photo
            : asset('storage/'.$this->photo);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
