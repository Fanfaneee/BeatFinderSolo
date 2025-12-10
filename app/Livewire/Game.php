<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Jeu;
use App\Models\Musique;
use Illuminate\Support\Facades\Auth;
use Livewore\Attributes\On;
use App\Livewire\Lobby;

class Game extends Component
{
    
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
    private const REVEAL_TIME = 2;

    
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
    // 1. Arrêter si la partie est terminée
    if ($this->roundStatus === 'finished' || $this->roundStatus === 'waiting') {
        return;
    }
    
    // 2. Décrémenter le minuteur (pour PLAYING ou REVEALED)
    if ($this->timeRemaining > 1) {
        $this->timeRemaining--;
        return; 
    }
    
    // 3. Le temps est écoulé (timeRemaining <= 1)
    
    if ($this->roundStatus === 'playing') {
        $this->endRound(false); // Passe à 'revealed'
       
        $this->startNextRound(); 
        
    } elseif ($this->roundStatus === 'revealed') {
}
}


    public function startNextRound()
    {
        if ($this->mancheActuelle >= $this->jeu->nombre_manches) { 
            $this->roundStatus = 'finished';
            $this->jeu->update(['status_enum' => 'terminé']);
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

    // 🔥 APPLICATION DU FILTRE
    if ($genreFiltre && $genreFiltre !== Lobby::GENRES_CHOIX[0]) {
        // App\Livewire\Lobby::GENRES_CHOIX[0] est 'Toutes Catégories'
        $query->where('genre', $genreFiltre);
    }
    $this->currentMusic = $query->inRandomOrder()->first();
    if (!$this->currentMusic) {
        // Cas d'erreur : plus de musique disponible sous ce genre/filtre.
        $this->answerMessage = "Plus de musiques disponibles dans la catégorie '{$genreFiltre}'. Fin de partie prématurée.";
        $this->roundStatus = 'finished';
        $this->jeu->update(['status_enum' => 'terminé']);
        return;
    }

    // 2. Ajouter l'ID de la nouvelle musique à l'historique
    $this->playedMusicIds[] = $this->currentMusic->id;
        
        if (!$this->currentMusic) {
            session()->flash('error', 'Catalogue de musiques vide.');
            $this->roundStatus = 'finished';
            return;
        }
        
    }

   public function endRound(bool $answeredImmediately)
{
    // 1. Mise à jour du statut et du score (inchangé)
    $this->roundStatus = 'revealed';
/*     $this->timeRemaining = self::REVEAL_TIME; 
 */    $this->jeu->update(['score' => $this->score]);
    
    
    if ($this->currentMusic) {
        
        $this->revealedMusics[] = [
            'manche' => $this->mancheActuelle,
            'titre' => $this->currentMusic->titre,
            'artiste' => $this->currentMusic->artiste,
            'image' => $this->currentMusic->image ? \Storage::url($this->currentMusic->image) : null,
            'score_gagne' => $this->score - $this->jeu->score, // Score gagné pendant cette manche
        ];
    }
    
    
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
    
    $this->validate(['userAnswer' => 'required|string|max:255']);
    
    // 2. Préparation
    $normalizedAnswer = $this->normalizeString($this->userAnswer);
    $correctTitle = $this->normalizeString($this->currentMusic->titre);
    $correctArtist = $this->normalizeString($this->currentMusic->artiste);
    
    $titleMatch = str_contains($normalizedAnswer, $correctTitle);
    $artistMatch = str_contains($normalizedAnswer, $correctArtist);
    
    $scoreGained = 0;
    $responseFound = false; // Indicateur pour savoir si quelque chose a été trouvé
    
    // 3. ÉVALUATION CUMULATIVE
    
    // A. Vérifier si le titre a été trouvé ET s'il ne l'était pas déjà
    if ($titleMatch && !$this->hasFoundTitle) {
        $scoreGained += 5; // J'augmente le score partiel pour le rendre plus visible
        $this->hasFoundTitle = true;
        $responseFound = true;
    }

    
    // B. Vérifier si l'artiste a été trouvé ET s'il ne l'était pas déjà
    if ($artistMatch && !$this->hasFoundArtist) {
        $scoreGained += 5; // J'augmente le score partiel
        $this->hasFoundArtist = true;
        $responseFound = true;
    }
    
    // 4. MISE À JOUR DU SCORE
    if ($scoreGained > 0) {
        $this->score += $scoreGained;
        
        // 5. VÉRIFICATION DE LA RÉPONSE COMPLÈTE
        if ($this->hasFoundTitle && $this->hasFoundArtist) {
            
            $this->hasFoundFullAnswer = true;
            $this->endRound(true); // Fin de manche immédiate
            $this->startNextRound();
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
