<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Schedule extends Model
{
    use HasUuids, HasFactory;

    public const STATUS_AVAILABLE = 'available';
    public const STATUS_LIMITED = 'limited';
    public const STATUS_FULLY_BOOKED = 'fully_booked';
    public const STATUS_BLOCKED = 'blocked';

    public const STATUSES = [
        self::STATUS_AVAILABLE,
        self::STATUS_LIMITED,
        self::STATUS_FULLY_BOOKED,
        self::STATUS_BLOCKED,
    ];

    public const BOOKABLE_STATUSES = [
        self::STATUS_AVAILABLE,
        self::STATUS_LIMITED,
    ];

    private const MAX_BOOKINGS_PER_SLOT = 2;

    protected $fillable = ['booking_date', 'booking_time', 'status', 'notes'];

    protected $casts = ['booking_date' => 'date'];

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function scopeAvailable($query)
    {
        return $query->whereIn('status', self::BOOKABLE_STATUSES);
    }

    public function scopeUnavailable($query)
    {
        return $query->whereIn('status', [self::STATUS_BLOCKED, self::STATUS_FULLY_BOOKED]);
    }

    public function scopeForMonth($query, int $year, int $month)
    {
        return $query->whereYear('booking_date', $year)
            ->whereMonth('booking_date', $month);
    }

    public function activeBookingsCount(): int
    {
        if (array_key_exists('active_bookings_count', $this->attributes)) {
            return (int) $this->attributes['active_bookings_count'];
        }

        return $this->bookings()
            ->whereNotIn('status', ['rejected'])
            ->count();
    }

    public function remainingSlots(?int $activeBookingsCount = null): int
    {
        if (! in_array($this->status, self::BOOKABLE_STATUSES, true)) {
            return 0;
        }

        $activeBookingsCount ??= $this->activeBookingsCount();

        return max(self::MAX_BOOKINGS_PER_SLOT - $activeBookingsCount, 0);
    }

    public function effectiveStatus(?int $activeBookingsCount = null): string
    {
        if ($this->status === self::STATUS_BLOCKED) {
            return self::STATUS_BLOCKED;
        }

        if ($this->status === self::STATUS_FULLY_BOOKED) {
            return self::STATUS_FULLY_BOOKED;
        }

        $activeBookingsCount ??= $this->activeBookingsCount();

        if ($activeBookingsCount >= self::MAX_BOOKINGS_PER_SLOT) {
            return self::STATUS_FULLY_BOOKED;
        }

        if ($this->status === self::STATUS_LIMITED || $activeBookingsCount > 0) {
            return self::STATUS_LIMITED;
        }

        return self::STATUS_AVAILABLE;
    }

    public function isBookable(?int $activeBookingsCount = null): bool
    {
        return $this->remainingSlots($activeBookingsCount) > 0;
    }

    public function syncStatusFromBookings(): void
    {
        if ($this->status === self::STATUS_BLOCKED) {
            return;
        }

        $activeBookingsCount = $this->activeBookingsCount();
        $nextStatus = match (true) {
            $activeBookingsCount >= self::MAX_BOOKINGS_PER_SLOT => self::STATUS_FULLY_BOOKED,
            $activeBookingsCount > 0 => self::STATUS_LIMITED,
            default => self::STATUS_AVAILABLE,
        };

        if ($this->status !== $nextStatus) {
            $this->update(['status' => $nextStatus]);
        }
    }

    public function timeLabel(): string
    {
        return substr((string) $this->booking_time, 0, 5);
    }
}
