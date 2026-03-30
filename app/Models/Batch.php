<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (Batch $batch) {
            if (! $batch->active) {
                return;
            }

            static::query()
                ->whereKeyNot($batch->getKey())
                ->update(['active' => false]);
        });
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }
}