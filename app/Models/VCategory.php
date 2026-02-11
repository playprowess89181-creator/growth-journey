<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VCategory extends Model
{
    protected $table = 'v_categories';

    protected $fillable = [
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function translations(): HasMany
    {
        return $this->hasMany(VCategoryTranslation::class, 'category_id');
    }

    public function words(): HasMany
    {
        return $this->hasMany(VocabularyWord::class, 'category_id');
    }
}
