<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hotel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'stars',
        'city',
        'address',
        'surcharge',
    ];

    protected $casts = [
        'stars' => 'integer',
        'surcharge' => 'float',
    ];

    public function tours(): BelongsToMany
    {
        return $this->belongsToMany(Tour::class)->withTimestamps();
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
