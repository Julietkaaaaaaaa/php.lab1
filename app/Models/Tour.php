<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tour extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'destination_country',
        'start_date',
        'duration_days',
        'base_price',
    ];

    protected $casts = [
        'start_date' => 'date',
        'duration_days' => 'integer',
        'base_price' => 'float',
    ];

    public function hotels(): BelongsToMany
    {
        return $this->belongsToMany(Hotel::class)->withTimestamps();
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}