<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PrayerRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'category',
        'is_public',
        'status',
    ];

    protected $casts = [
        'is_public' => 'bool',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function prayers(): HasMany
    {
        return $this->hasMany(PrayerRequestPrayer::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(PrayerRequestComment::class);
    }

    public function approvedComments(): HasMany
    {
        return $this->hasMany(PrayerRequestComment::class)->where('is_approved', true);
    }
}
