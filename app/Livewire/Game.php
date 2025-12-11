<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Jeu;
use App\Models\Musique;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On; // Correction de la faute de frappe : Livewire\Attributes\On
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
    
    // CONSTANTES
    private const READING_TIME = 15;
    // La constante REVEAL_TIME est conservée mais ignorée dans tick() selon votre demande.

    
    public function mount(int $gameId)
    {
        $this->jeu = Jeu::findOrFail($gameId);
        
        if ($this->jeu->user_id !== Auth::id()) {
            abort(403, 'Accès interdit à cette partie.');
        }

        $this->score = $this->jeu->score;
        
        // Initialiser l'historique des musiques jouées (si besoin de persistance)
        // Pour l'instant, on se base sur les musiques jouées durant cette session
        
        if ($this->jeu->status_enum === 'en_cours') {
            $this->startNextRound();
        } else {
            $this->roundStatus = 'finished';
        }
    }
    

    public function tick()
    {
        // 1. Arrêter si la partie est terminée ou en attente
        if ($this->roundStatus === 'finished' || $this->roundStatus === 'waiting') {
            return;
        }
        
        // 2. Décrémenter le minuteur uniquement si nous sommes en phase de jeu
        if ($this->roundStatus === 'playing') {
            
            if ($this->timeRemaining > 1) {
                $this->timeRemaining--;
                return; 
            }
            
            // 3. Le temps est écoulé (timeRemaining <= 1)
            
            // La manche de jeu se termine sans réponse complète
            $this->endRound(false); // Passe le statut à 'revealed'
            $this->startNextRound(); // Commence immédiatement la prochaine manche ou termine le jeu
        } 
        // L'état 'revealed' est ignoré ici, car la transition est gérée immédiatement dans endRound -> startNextRound
    }

    public function startNextRound()
    {
        // 🚨 1. VÉRIFICATION DE LA FIN DE PARTIE NORMALE (Nombre de manches atteint)
        if ($this->mancheActuelle >= $this->jeu->nombre_manches) { 
            
            $this->roundStatus = 'finished';
/*             dd('Fin de partie atteinte! Score à envoyer:', $this->score, 'Catégorie:', $this->jeu->genre_filtre);
 */            
            // 🔥 Mise à jour finale du score et du statut dans la DB
            $this->jeu->update(['status_enum' => 'terminé', 'score' => $this->score]);
            
            // 🔥 DISPATCH L'ÉVÉNEMENT POUR ENREGISTRER LE MEILLEUR SCORE (ScoreSaver)
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

        // 🔥 Accès sécurisé à la constante de l'autre classe Lobby (évite les erreurs de classe non trouvée)
        $allCategoriesOption = constant(Lobby::class . '::GENRES_CHOIX')[0] ?? 'Toutes Catégories';

        if ($genreFiltre && $genreFiltre !== $allCategoriesOption) {
            // Le champ 'genre' dans musiques doit correspondre à la valeur de Lobby::GENRES_CHOIX
            $query->where('genre', $genreFiltre);
        }
        
        $this->currentMusic = $query->inRandomOrder()->first();
        
        // 🚨 2. VÉRIFICATION DE LA FIN DE PARTIE PRÉMATURÉE (Plus de musique)
        if (!$this->currentMusic) {
            
            $this->answerMessage = "Plus de musiques disponibles dans la catégorie '{$genreFiltre}'. Fin de partie prématurée.";
            $this->roundStatus = 'finished';

            // 🔥 Mise à jour finale du score et du statut dans la DB
            $this->jeu->update(['status_enum' => 'terminé', 'score' => $this->score]);
            
            // 🔥 DISPATCH L'ÉVÉNEMENT POUR ENREGISTRER LE MEILLEUR SCORE
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
                
                $this->answerMessage = "🥇 FÉLICITATIONS ! Réponse complète trouvée (Total: +{$scoreGained} pts) !";
                
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