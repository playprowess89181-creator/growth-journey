<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrackHabitMemberStat extends Model
{
    use HasFactory;

    protected $fillable = [
        'community_group_id',
        'track_habit_id',
        'user_id',
        'stat_date',
        'status',
        'streak',
        'overall_percentage',
        'is_frozen',
    ];

    protected $casts = [
        'stat_date' => 'date',
        'streak' => 'integer',
        'overall_percentage' => 'integer',
        'is_frozen' => 'boolean',
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
