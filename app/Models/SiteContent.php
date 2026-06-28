<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteContent extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value', 'type'];

    public static function get(string $key, string $default = ''): string
    {
        return static::where('key', $key)->value('value') ?? $default;
    }
}
