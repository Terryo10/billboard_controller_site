<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'station_id',
        'status',
        'total_price',
        'discount_amount',
        'slot_count',
        'paid_at',
        'notes',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'total_price' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }

    public function bookingSlots(): HasMany
    {
        return $this->hasMany(BookingSlot::class);
    }

    public function advert(): HasOne
    {
        return $this->hasOne(Advert::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function isPaid(): bool
    {
        return $this->paid_at !== null;
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function calculatePrice(): float
    {
        $base = (float) $this->bookingSlots->sum(fn ($slot) => $slot->slotTemplate->price);
        $count = $this->bookingSlots->count();

        // Bulk discount: 10% for 5+, 20% for 10+
        $discountRate = match (true) {
            $count >= 10 => 0.20,
            $count >= 5  => 0.10,
            default      => 0.00,
        };

        $this->discount_amount = $base * $discountRate;
        $this->total_price = $base - $this->discount_amount;
        $this->slot_count = $count;

        return (float) $this->total_price;
    }
}
