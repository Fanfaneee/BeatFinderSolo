<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\MeilleursScore;
use App\Models\Jeu;

class Profil extends Component
{
    public $user;
    public string $defaultAvatarUrl = 'https://api.dicebear.com/9.x/big-ears/svg?seed='; 
    public $meilleursScores;
    public $absoluteBestScore ;
    public $gamesPlayedCount;

    public function mount()
    {
        $this->user = Auth::user()->load('meilleursScores'); // changer meilleur scores pour l' utilisateusurs
        $this->meilleursScores = $this->user->meilleursScores->sortByDesc('score'); //trie les scores par catégorie
        if ($this->meilleursScores->isNotEmpty()) {
            $this->absoluteBestScore = $this->meilleursScores->max('score'); // calcul le score max
        }
        $this->gamesPlayedCount = Jeu::where('user_id', Auth::id())
                                     ->where('status_enum', 'terminé') // Compte  les parties terminées pour le nbre de parties
                                     ->count();
    }
    public function render()
    {
        return view('livewire.profil');
    }
    public function getAvatarUrl(): string
    {
        if (!$this->user) {
            
            return $this->defaultAvatarUrl . 'guest'; 
        }

        if ($this->user->avatar_url) {
            return $this->user->avatar_url;
        }
        
        $seed = urlencode($this->user->username);
        $options = '&backgroundColor[]=b6e3f4&backgroundColor[]=c0aede&radius=50'; 
        
        
        return $this->defaultAvatarUrl . $seed . $options; 
    }
}