<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrackHabit extends Model
{
    use HasFactory;

    protected $fillable = [
        'community_group_id',
        'name',
        'description',
        'frequency_type',
        'frequency_label',
        'weekdays',
        'times_per_week',
        'xp',
    ];

    protected $casts = [
        'weekdays' => 'array',
        'times_per_week' => 'integer',
        'xp' => 'integer',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(CommunityGroup::class, 'community_group_id');
    }

    public function memberStats()
    {
        return $this->hasMany(TrackHabitMemberStat::class, 'track_habit_id');
    }
}
