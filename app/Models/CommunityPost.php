<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommunityPost extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'content',
        'image',
        'community_group_id',
        'user_id',
        'is_published',
        'is_pinned',
        'published_at',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'is_pinned' => 'boolean',
        'published_at' => 'datetime',
    ];

    /**
     * Get the community group that owns the post.
     */
    public function communityGroup(): BelongsTo
    {
        return $this->belongsTo(CommunityGroup::class);
    }

    /**
     * Get the user who created the post.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the comments for the post.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get the reports for this post.
     */
    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    /**
     * Scope to get only published posts.
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope to get only pinned posts.
     */
    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    /**
     * Scope to get posts by community group.
     */
    public function scopeByGroup($query, $groupId)
    {
        return $query->where('community_group_id', $groupId);
    }

    /**
     * Get the post's excerpt.
     */
    public function getExcerptAttribute($length = 150)
    {
        return strlen($this->content) > $length
            ? substr($this->content, 0, $length).'...'
            : $this->content;
    }

    /**
     * Get the comments count.
     */
    public function getCommentsCountAttribute()
    {
        return $this->comments()->count();
    }
}
