<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Package extends Model
{
    use HasUuids, HasFactory;

    protected $fillable = [
        'package_name', 'description', 'price', 'image', 'status',
    ];

    protected $casts = ['price' => 'decimal:2'];

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function getImageUrlAttribute(): string
    {
        return $this->image
            ? Storage::disk(config('filesystems.public_uploads_disk'))->url($this->image)
            : asset('images/placeholder.jpg');
    }
}
