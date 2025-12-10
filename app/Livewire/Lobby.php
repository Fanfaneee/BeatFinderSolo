<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Jeu;
use Illuminate\Support\Facades\Auth;

class Lobby extends Component
{

    public string $gameName;
    public int $nombreManches= 5;
    public ?int $gameId= null;
    public string $selectedGenre = '';

    //pour carousel
    public static $GENRES_CHOIX = ['Pop', 'Rock', 'Hip Hop', 'Années 80', 'Hits 2020', 'Jazz']; // Exemple
    public $currentSlideIndex = 0;
    //

    const GENRES_CHOIX = [
        'Toutes Catégories', // Option pour inclure tous les genres
        'Rock Classique', 
        'Pop / Variété Internationale',
        'Hip-Hop / Rap Français', 
        'Electro / Dance',
    ];

   protected array $rules = [
        'gameName' => 'required|string|min:3|max:50',
        'nombreManches' => 'required|integer|min:3|max:50',
    ];
    
    public function mount() {
        if (Auth::check()) {
        // 🔥 CORRECTION : Accédez à la propriété 'username', pas à la méthode 'username()'
        $this->gameName = "Partie de " . Auth::user()->username;
    } else {
        $this->gameName = "Partie Blind Test"; 
    
    }
}

    public function createGame() {
        // Validez uniquement les champs de base ici, car la sélection se fera via boutons
        $this->validate([
            'gameName' => 'required|string|min:3|max:50',
            'nombreManches' => 'required|integer|min:3|max:50',
        ]);
        
        $jeu = Jeu::create([
            'user_id' => Auth::id(), 
            'name' => $this->gameName,
            'status_enum' => 'en_cours',
            'score' => 0,
            'nombre_manches' => $this->nombreManches,
            // 🔥 Sauvegarde du genre sélectionné
            'genre_filtre' => $this->selectedGenre, 
        ]);
        
        $this->gameId = $jeu->id;
        
        return $this->redirect(route('game', ['gameId' => $jeu->id]), navigate: true);
    }

    public function selectGenre(string $genre)
    {
        $this->selectedGenre = $genre;
        
        // Vous pouvez ajuster le nom du jeu ici si vous le souhaitez
        if ($genre !== self::GENRES_CHOIX[0]) {
            $this->gameName = "Partie " . $genre;
        } else {
             $this->gameName = "Partie Toutes Catégories";
        }
    }


    public function resetState()
    {
        // Réinitialise l'état pour forcer l'affichage de l'étape 1
        $this->selectedGenre = '';
        $this->gameId = null; 
        
        // Optionnel : Réinitialiser le nom du jeu à la valeur par défaut
        if (Auth::check()) {
            $this->gameName = "Partie de " . Auth::user()->username;
        } else {
            $this->gameName = "Partie Blind Test"; 
        }
    }


public function nextSlide()
    {
        // On utilise la propriété de classe 'const' pour avoir la liste des genres
        $maxIndex = count(self::GENRES_CHOIX) - 1; 

        if ($this->currentSlideIndex < $maxIndex) {
            // Avance normalement
            $this->currentSlideIndex++;
        } else {
            // BOUCLAGE : Si on est à la dernière slide, on revient à la première (index 0)
            $this->currentSlideIndex = 0; 
        }
    }

    public function prevSlide()
    {
        // On utilise la propriété de classe 'const' pour avoir la liste des genres
        $maxIndex = count(self::GENRES_CHOIX) - 1; 

        if ($this->currentSlideIndex > 0) {
            // Recule normalement
            $this->currentSlideIndex--;
        } else {
            // BOUCLAGE : Si on est à la première slide, on va à la dernière
            $this->currentSlideIndex = $maxIndex; 
        }
    }


    public function render(){
        return view('livewire.lobby');
    }
}
