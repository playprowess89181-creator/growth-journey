<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModuleTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'module_id',
        'language_code',
        'title',
        'description',
    ];

    public function module()
    {
        return $this->belongsTo(Module::class);
    }
}
