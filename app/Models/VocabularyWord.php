<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VocabularyWord extends Model
{
    protected $table = 'vocabulary_words';

    protected $fillable = [
        'category_id',
        'word_key',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(VCategory::class, 'category_id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(VWordTranslation::class, 'word_id');
    }
}
