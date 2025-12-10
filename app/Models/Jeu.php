<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jeu extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'status_enum',
        'nombre_manches',
        'score',
        'genre_filtre',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
}


