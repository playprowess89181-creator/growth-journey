<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DialogueTopicUpvote extends Model
{
    use HasFactory;

    protected $fillable = [
        'dialogue_topic_id',
        'user_id',
    ];

    public function topic(): BelongsTo
    {
        return $this->belongsTo(DialogueTopic::class, 'dialogue_topic_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
