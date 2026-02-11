<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'level_id',
        'order',
        'status',
        'difficulty',
    ];

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function translations()
    {
        return $this->hasMany(LessonTranslation::class);
    }
}
