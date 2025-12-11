<?php
// database/migrations/<timestamp>_create_meilleurs_scores_table.php

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
        Schema::create('meilleurs_scores', function (Blueprint $table) {
            $table->id(); // id bigint [pk]

            // Clé étrangère vers la table users
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            $table->integer('score')->unsigned(); // score int (unsigned pour éviter les scores négatifs)
            $table->string('categorie'); // catégorie varchar
            
            $table->timestamp('date_score'); // date_score timestamp (peut être omis si vous utilisez created_at/updated_at)

            // Ajout d'une colonne unique composée pour garantir un seul meilleur score par utilisateur et par catégorie
            // $table->unique(['user_id', 'categorie']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meilleurs_scores');
    }
};