<?php
// app/Models/Musique.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Musique extends Model
{
    use HasFactory;

    /**
     * Les attributs qui peuvent être assignés en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'titre',
        'artiste',
        'extract', // Chemin du fichier audio
        'image',   // Chemin du fichier image (pochette)
        'annee',   // Année de sortie du morceau
        'genre',  // Genre musical
    ];
    
    // Si la colonne 'annee' est rarement utilisée, vous pouvez la retirer des fillables.
}