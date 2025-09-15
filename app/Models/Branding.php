<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branding extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value', 'updated_by'];

    public $timestamps = true;

    public static function getValue(string $key, mixed $default = null): mixed
    {
        $item = static::where('key', $key)->first();
        return $item?->value ?? $default;
    }

    public static function setValue(string $key, mixed $value, ?int $updatedBy = null): void
    {
        static::updateOrCreate(['key' => $key], [
            'value' => $value,
            'updated_by' => $updatedBy,
        ]);
    }
}


