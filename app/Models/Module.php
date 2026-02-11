<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    protected $fillable = [
        'status',
        'order',
    ];

    public function translations()
    {
        return $this->hasMany(ModuleTranslation::class);
    }

    public function levels()
    {
        return $this->hasMany(Level::class);
    }
}
