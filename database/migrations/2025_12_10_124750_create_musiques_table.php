<?php
// database/migrations/..._create_musiques_table.php

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
        Schema::create('musiques', function (Blueprint $table) {
            $table->id();
            
            // Informations sur le morceau
            $table->string('titre');
            $table->string('artiste');            
            // Chemin vers le fichier d'extrait audio
            $table->string('extract')->unique(); 
            
            // Chemin vers le fichier image de la pochette
            $table->string('image')->nullable();
            // Année de sortie du morceau
            $table->year('annee')->nullable();
            // Genre musical
            $table->string('genre')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('musiques');
    }
};