<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VCategoryTranslation extends Model
{
    protected $table = 'v_category_translations';

    protected $fillable = [
        'category_id',
        'language_code',
        'title',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(VCategory::class, 'category_id');
    }
}
