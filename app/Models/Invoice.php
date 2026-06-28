<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Invoice extends Model
{
    use HasUuids, HasFactory;

    protected $fillable = [
        'booking_id', 'invoice_number', 'invoice_date', 'total_price', 'shipping_cost', 'pdf_path',
    ];

    protected $casts = [
        'invoice_date'  => 'date',
        'total_price'   => 'decimal:2',
        'shipping_cost' => 'decimal:2',
    ];

    public function getGrandTotalAttribute(): float
    {
        return (float) $this->total_price + (float) $this->shipping_cost;
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function getPdfUrlAttribute(): ?string
    {
        return $this->pdf_path
            ? Storage::disk(config('filesystems.invoice_disk'))->url($this->pdf_path)
            : null;
    }
}
