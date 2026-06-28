<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Gallery extends Model
{
    use HasUuids, HasFactory;

    protected $fillable = ['image_path', 'caption', 'category', 'sort_order'];

    public function getImageUrlAttribute(): string
    {
        return Storage::disk(config('filesystems.public_uploads_disk'))->url($this->image_path);
    }
}
