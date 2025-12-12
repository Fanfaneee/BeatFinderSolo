<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\MeilleursScore;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On; 
class ScoreSaver extends Component
{
    // Rendre la méthode render() triviale car ce composant n'a pas de vue visible
    public function render()
    {
        return view('livewire.score-saver');
    }

    
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
        $GLOBAL_KEY = 'Global'; // Clé pour le meilleur score absolu
        
        $currentRecord = null; // Record de la catégorie spécifique (si ce n'est pas "Toutes Catégories")

        // ---------------------------------------------------------------------
        // 1. Sauvegarde/Mise à jour du score par CATÉGORIE SPÉCIFIQUE
        // ---------------------------------------------------------------------
        
        // Si la partie jouée n'était PAS le mode "Toutes Catégories", on l'enregistre
        if ($categorie !== 'Toutes Catégories') {
            
            $existingCatScore = MeilleursScore::where('user_id', $userId)
                                            ->where('categorie', $categorie)
                                            ->first();
            
            // On utilise updateOrCreate (ou la logique If/Else If) pour la mise à jour conditionnelle
            if (!$existingCatScore || $score > $existingCatScore->score) {
                $currentRecord = MeilleursScore::updateOrCreate(
                    ['user_id' => $userId, 'categorie' => $categorie],
                    ['score' => $score, 'date_score' => now()]
                );
            } else {
                $currentRecord = $existingCatScore;
            }
        }

        // ---------------------------------------------------------------------
        // 2. Sauvegarde/Mise à jour du score GLOBAL (Meilleur Absolu)
        //    (Ceci est mis à jour même si la partie jouée était une catégorie spécifique)
        // ---------------------------------------------------------------------
        
        $existingGlobalScore = MeilleursScore::where('user_id', $userId)
                                            ->where('categorie', $GLOBAL_KEY)
                                            ->first();

        // Si le score actuel est meilleur que le record Global (ou si le record n'existe pas)
        if (!$existingGlobalScore || $score > $existingGlobalScore->score) {
            MeilleursScore::updateOrCreate(
                ['user_id' => $userId, 'categorie' => $GLOBAL_KEY],
                ['score' => $score, 'date_score' => now()]
            );
        }
        
        // Retourne le record spécifique si trouvé, sinon null
        return $currentRecord; 
    }
}