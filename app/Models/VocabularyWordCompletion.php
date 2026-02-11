<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VocabularyWordCompletion extends Model
{
    protected $table = 'vocabulary_word_completions';

    protected $fillable = [
        'user_id',
        'word_id',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function word(): BelongsTo
    {
        return $this->belongsTo(VocabularyWord::class, 'word_id');
    }
}
