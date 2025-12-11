<?php
// app/Models/MeilleurScore.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeilleursScore extends Model
{
    use HasFactory;

    // Définition du nom de la table
    protected $table = 'meilleurs_scores';

    // Champs que vous permettez d'être remplis massivement
    protected $fillable = [
        'user_id',
        'score',
        'categorie',
        'date_score',
    ];
    
    // Indiquer que nous n'utilisons pas les colonnes created_at et updated_at par défaut
    public $timestamps = false;
    
    // Relation : Un meilleur score appartient à un utilisateur
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}