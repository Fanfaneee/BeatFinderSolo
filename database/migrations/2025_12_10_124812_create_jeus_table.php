<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jeus', function (Blueprint $table) {
            $table->id();
            
            // 1. Clé étrangère vers la table users
            // Assurez-vous que cette clé pointe vers le modèle 'User'
            $table->foreignId('user_id')
                  ->constrained() // Crée l'index et la contrainte de clé étrangère
                  ->onDelete('cascade'); // Si l'utilisateur est supprimé, ses jeux le sont aussi

            // 2. Attributs du jeu
            $table->string('name')->default('Partie Blind Test Solo');
            $table->string('status_enum')->default('en_cours'); // Statut: en_cours, terminé, etc.
            $table->integer('score')->default(0); // Le score de la partie
            $table->integer('nombre_manches')->default(5); // Nombre de manches dans le jeu
            $table->string('genre_filtre')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jeus');
    }
};