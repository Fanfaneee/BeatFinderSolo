<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\MeilleursScore;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On; // 🔥 NÉCESSAIRE pour écouter l'événement 'gameFinished'

class ScoreSaver extends Component
{
    // Rendre la méthode render() triviale car ce composant n'a pas de vue visible
    public function render()
    {
        return view('livewire.score-saver');
    }

    /**
     * 🔥 NOUVELLE MÉTHODE : Écoute l'événement 'gameFinished' déclenché par Game.php.
     */
    #[On('gameFinished')]
    public function handleGameFinished(int $score, string $categorie)
    {
/*         dd('ScoreSaver received event!', ['score' => $score, 'categorie' => $categorie]);
 */        // Appelle la logique de sauvegarde avec les données reçues
        $this->saveBestScore($score, $categorie);

        // Optionnel : Déclenche un événement pour rafraîchir d'autres composants comme Classement
        $this->dispatch('scoresUpdated');
    }

    /**
     * Méthode principale pour enregistrer ou mettre à jour le meilleur score.
     */
    public function saveBestScore(int $score, string $categorie): ?MeilleursScore
    {
        if (!Auth::check()) {
            return null;
        }

        $userId = Auth::id();

        // 1. Trouver l'enregistrement existant pour cet utilisateur/catégorie
        $existingBestScore = MeilleursScore::where('user_id', $userId)
                                          ->where('categorie', $categorie)
                                          ->first();

        // 2. Si un score existe ET que le nouveau score est PLUS GRAND
        if ($existingBestScore) {
            if ($score > $existingBestScore->score) {
                $existingBestScore->score = $score;
                $existingBestScore->date_score = now();
                $existingBestScore->save();
                return $existingBestScore;
            }
            // Si le nouveau score est inférieur ou égal, on ne fait rien
            return $existingBestScore;
        }
        
        // 3. Si aucun score n'existe, on le crée
        return MeilleursScore::create([
            'user_id' => $userId,
            'score' => $score,
            'categorie' => $categorie,
            'date_score' => now(), 
        ]);
    }
}