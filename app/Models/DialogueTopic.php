<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DialogueTopic extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'status',
        'created_by',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(DialogueComment::class);
    }

    public function approvedComments(): HasMany
    {
        return $this->hasMany(DialogueComment::class)->where('is_approved', true);
    }

    public function upvotes(): HasMany
    {
        return $this->hasMany(DialogueTopicUpvote::class);
    }
}
