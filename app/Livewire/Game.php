<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Jeu;
use App\Models\Musique;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On; 
use App\Livewire\Lobby;

class Game extends Component
{
    // PROPRIÉTÉS
    public Jeu $jeu;
    public $currentMusic = null; 
    public int $score;
    public int $mancheActuelle = 0;
    public int $timeRemaining = 15;
    public string $roundStatus = 'waiting'; // 'playing', 'revealed', 'finished'
    public string $userAnswer = '';
    public ?string $answerMessage = null;
    public bool $hasFoundFullAnswer = false; 
    public bool $hasFoundTitle = false;
    public bool $hasFoundArtist = false;
    public array $revealedMusics = [];
    public array $playedMusicIds = [];
    
    
    private const READING_TIME = 15;
   

    
    public function mount(int $gameId)
    {
        $this->jeu = Jeu::findOrFail($gameId);
        
        if ($this->jeu->user_id !== Auth::id()) {
            abort(403, 'Accès interdit à cette partie.');
        }

        $this->score = $this->jeu->score;
        
      
        
        if ($this->jeu->status_enum === 'en_cours') {
            $this->startNextRound();
        } else {
            $this->roundStatus = 'finished';
        }
    }
    

    public function tick()
    {
       
        if ($this->roundStatus === 'finished' || $this->roundStatus === 'waiting') {
            return;
        }
        
       
        if ($this->roundStatus === 'playing') {
            
            if ($this->timeRemaining > 1) {
                $this->timeRemaining--;
                return; 
            }
            
           
            $this->endRound(false);
            $this->startNextRound(); 
        } 
    }

    public function startNextRound()
    {
        if ($this->mancheActuelle >= $this->jeu->nombre_manches) { 
            
            $this->roundStatus = 'finished';
/*             dd('Fin de partie atteinte! Score à envoyer:', $this->score, 'Catégorie:', $this->jeu->genre_filtre);
 */            
            $this->jeu->update(['status_enum' => 'terminé', 'score' => $this->score]);
            
            $this->dispatch('gameFinished', score: $this->score, categorie: $this->jeu->genre_filtre);
            
            return;
        }

        $this->mancheActuelle++;
        $this->timeRemaining = self::READING_TIME;
        $this->roundStatus = 'playing';
        $this->userAnswer = '';
        $this->answerMessage = null;
        $this->hasFoundFullAnswer = false;
        $this->hasFoundTitle = false;
        $this->hasFoundArtist = false;

        $genreFiltre = $this->jeu->genre_filtre;
        $query = Musique::whereNotIn('id', $this->playedMusicIds);

        $allCategoriesOption = constant(Lobby::class . '::GENRES_CHOIX')[0] ?? 'Toutes Catégories';

        if ($genreFiltre && $genreFiltre !== $allCategoriesOption) {
            $query->where('genre', $genreFiltre);
        }
        
        $this->currentMusic = $query->inRandomOrder()->first();
        
        if (!$this->currentMusic) {
            
            $this->answerMessage = "Plus de musiques disponibles dans la catégorie '{$genreFiltre}'. Fin de partie prématurée.";
            $this->roundStatus = 'finished';

           
            $this->jeu->update(['status_enum' => 'terminé', 'score' => $this->score]);
            
            $this->dispatch('gameFinished', score: $this->score, categorie: $this->jeu->genre_filtre);
            
            return;
        }

        // 3. Ajouter l'ID de la nouvelle musique à l'historique
        $this->playedMusicIds[] = $this->currentMusic->id;
        
        // Supprime le bloc if (!$this->currentMusic) en double à la fin
    }

    public function endRound(bool $answeredImmediately)
    {
        // 1. Mise à jour du statut
        $this->roundStatus = 'revealed';
        // NE PAS toucher au timeRemaining, car il doit rester à 0 si l'on ne veut pas de pause.
        
        // Enregistrer le score cumulé dans la partie (cela est mis à jour à chaque manche)
        $this->jeu->update(['score' => $this->score]);
        
        
        if ($this->currentMusic) {
            // Le score gagné est la différence entre le nouveau score et l'ancien score stocké dans la DB (avant update)
            $scoreGagneCetteManche = $this->score - $this->jeu->score; 

            // Logique de l'historique de la manche (si besoin)
            $this->revealedMusics[] = [
                'manche' => $this->mancheActuelle,
                'titre' => $this->currentMusic->titre,
                'artiste' => $this->currentMusic->artiste,
                'image' => $this->currentMusic->image ? \Storage::url($this->currentMusic->image) : null,
                'score_gagne' => $scoreGagneCetteManche, 
            ];
        }
        
        // Message final
        if (!$answeredImmediately) {
            $this->answerMessage = "Temps écoulé !";
        }
    }


    public function submitAnswer()
    {
        // 1. Vérification de l'état (inchangée)
        if ($this->roundStatus !== 'playing' || $this->hasFoundFullAnswer || is_null($this->currentMusic)) {
            $this->answerMessage = "Vous ne pouvez pas répondre maintenant.";
            return;
        }
        
        // ... (Logique de vérification, normalisation, calcul du score, etc. reste inchangée) ...
        
        $this->validate(['userAnswer' => 'required|string|max:255']);
        
        $normalizedAnswer = $this->normalizeString($this->userAnswer);
        $correctTitle = $this->normalizeString($this->currentMusic->titre);
        $correctArtist = $this->normalizeString($this->currentMusic->artiste);
        
        $titleMatch = str_contains($normalizedAnswer, $correctTitle);
        $artistMatch = str_contains($normalizedAnswer, $correctArtist);
        
        $scoreGained = 0;
        $responseFound = false;
        
        // ÉVALUATION CUMULATIVE
        if ($titleMatch && !$this->hasFoundTitle) {
            $scoreGained += 5; 
            $this->hasFoundTitle = true;
            $responseFound = true;
        }

        if ($artistMatch && !$this->hasFoundArtist) {
            $scoreGained += 5; 
            $this->hasFoundArtist = true;
            $responseFound = true;
        }
        
        // 4. MISE À JOUR DU SCORE
        if ($scoreGained > 0) {
            $this->score += $scoreGained;
            
            // 5. VÉRIFICATION DE LA RÉPONSE COMPLÈTE
            if ($this->hasFoundTitle && $this->hasFoundArtist) {
                
                $this->hasFoundFullAnswer = true;
                $this->endRound(true); // Passe à 'revealed' et enregistre le score
                $this->startNextRound(); // Commence la prochaine manche/termine le jeu
                
                $this->answerMessage = "FÉLICITATIONS ! Réponse complète trouvée (Total: +{$scoreGained} pts) !";
                
            } else {
                // Réponse partielle ou nouvel élément trouvé
                $this->answerMessage = "Bonne réponse partielle (+{$scoreGained} pts) ! Continuez !";
            }
        } elseif ($responseFound) {
            // Cas où l'utilisateur a trouvé quelque chose mais l'avait DÉJÀ trouvé
            $this->answerMessage = "Ce titre/artiste était déjà enregistré. Réessayez !";
        }
        
        // 6. CAS AUCUNE CORRESPONDANCE
        else {
            $this->answerMessage = "Mauvaise réponse. Réessayez.";
        }
        
        $this->userAnswer = '';
    }
    
    // ... (normalizeString() et render() restent inchangés) ...
    private function normalizeString(string $string): string
    {
        $string = strtolower($string);
        $string = str_replace(
            ['á', 'é', 'í', 'ó', 'ú', 'à', 'è', 'ì', 'ò', 'ù', 'ä', 'ë', 'ï', 'ö', 'ü', 'ç'],
            ['a', 'e', 'i', 'o', 'u', 'a', 'e', 'i', 'o', 'u', 'a', 'e', 'i', 'o', 'u', 'c'],
            $string
        );
        $string = preg_replace('/[^a-z0-9\s]/', '', $string);
        $string = preg_replace('/\s+/', ' ', $string);
        return trim($string);
    }
    
    public function render()
    {
        return view('livewire.game');
    }
}