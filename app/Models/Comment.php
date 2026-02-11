<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'content',
        'community_post_id',
        'user_id',
        'is_approved',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
    ];

    /**
     * Get the post that owns the comment.
     */
    public function communityPost(): BelongsTo
    {
        return $this->belongsTo(CommunityPost::class)->withTrashed();
    }

    /**
     * Get the user who created the comment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get only approved comments.
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope to get only pending comments.
     */
    public function scopePending($query)
    {
        return $query->where('is_approved', false);
    }

    /**
     * Scope to get comments by post.
     */
    public function scopeByPost($query, $postId)
    {
        return $query->where('community_post_id', $postId);
    }

    /**
     * Get the comment's excerpt.
     */
    public function getExcerptAttribute($length = 100)
    {
        return strlen($this->content) > $length
            ? substr($this->content, 0, $length).'...'
            : $this->content;
    }

    /**
     * Get the reports for this comment.
     */
    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }
}
