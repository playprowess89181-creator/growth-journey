<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VWordTranslation extends Model
{
    protected $table = 'v_word_translations';

    protected $fillable = [
        'word_id',
        'language_code',
        'word_text',
        'meaning_text',
    ];

    public function word(): BelongsTo
    {
        return $this->belongsTo(VocabularyWord::class, 'word_id');
    }
}
