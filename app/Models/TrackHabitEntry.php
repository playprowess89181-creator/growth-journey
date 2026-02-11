<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrackHabitEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'community_group_id',
        'track_habit_id',
        'user_id',
        'entry_date',
    ];

    protected $casts = [
        'entry_date' => 'array',
    ];

    public function habit(): BelongsTo
    {
        return $this->belongsTo(TrackHabit::class, 'track_habit_id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(CommunityGroup::class, 'community_group_id');
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
