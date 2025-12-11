<?php
// app/Livewire/Profil.php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\MeilleursScore;
use App\Models\Jeu;

class Profil extends Component
{
    public $user;
    // La propriété est nommée $defaultAvatarUrl
    public string $defaultAvatarUrl = 'https://api.dicebear.com/9.x/big-ears/svg?seed='; 
    public $meilleursScores;
    public $absoluteBestScore ;
    public $gamesPlayedCount;

    public function mount()
    {
        $this->user = Auth::user()->load('meilleursScores');
        
        // 2. Trie les meilleurs scores par catégorie
        $this->meilleursScores = $this->user->meilleursScores->sortByDesc('score');

        // 3. Calcule le meilleur score absolu (le max de tous les scores par catégorie)
        if ($this->meilleursScores->isNotEmpty()) {
            $this->absoluteBestScore = $this->meilleursScores->max('score');
        }

        // 4. Compte le nombre de parties jouées (basé sur votre table Jeu/sessions_solo)
        // Ceci suppose que votre table s'appelle 'Jeu' (ou 'sessions_solo')
        $this->gamesPlayedCount = Jeu::where('user_id', Auth::id())
                                     ->where('status_enum', 'terminé') // Compte seulement les parties terminées
                                     ->count();
    }

    public function render()
    {
        return view('livewire.profil');
    }

    public function getAvatarUrl(): string
    {
        if (!$this->user) {
            // Utilisation corrigée : $this->defaultAvatarUrl
            return $this->defaultAvatarUrl . 'guest'; 
        }

        if ($this->user->avatar_url) {
            return $this->user->avatar_url;
        }
        
        $seed = urlencode($this->user->username);
        $options = '&backgroundColor[]=b6e3f4&backgroundColor[]=c0aede&radius=50'; 
        
        // Utilisation corrigée : $this->defaultAvatarUrl
        return $this->defaultAvatarUrl . $seed . $options; 
    }
}