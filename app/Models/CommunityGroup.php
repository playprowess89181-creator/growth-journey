<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommunityGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'category',
        'image',
        'max_members',
        'is_active',
        'settings',
        'created_by',
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
        'max_members' => 'integer',
    ];

    /**
     * Get the user who created this group.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the posts for this group.
     */
    public function posts()
    {
        return $this->hasMany(CommunityPost::class, 'community_group_id');
    }

    /**
     * Get the members of this group.
     */
    public function members()
    {
        return $this->belongsToMany(User::class, 'community_group_user')
            ->withPivot('joined_at')
            ->withTimestamps();
    }

    public function trackHabits()
    {
        return $this->hasMany(TrackHabit::class, 'community_group_id');
    }

    /**
     * Scope to get only active groups.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get groups by category.
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Get the status label for display.
     */
    public function getStatusLabelAttribute()
    {
        return $this->is_active ? 'Active' : 'Inactive';
    }
}
