<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TimeSlotTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'station_id',
        'day_of_week',
        'start_time',
        'end_time',
        'duration_seconds',
        'price',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }

    public function bookingSlots(): HasMany
    {
        return $this->hasMany(BookingSlot::class, 'slot_template_id');
    }

    public function getDayNameAttribute(): string
    {
        return ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'][$this->day_of_week] ?? '';
    }

    public function isBookedOn(string $date): bool
    {
        return $this->bookingSlots()
            ->where('air_date', $date)
            ->whereHas('booking', fn ($q) => $q->whereIn('status', ['pending', 'approved']))
            ->exists();
    }
}
