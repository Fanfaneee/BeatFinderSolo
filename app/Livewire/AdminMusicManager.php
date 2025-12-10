<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Musique;

use Livewire\WithFileUploads; 
class AdminMusicManager extends Component
{
    use WithFileUploads;
    public $extract;
    public string $titre = '';
    public string $artiste = '';
    public string $genre = '';
    public  $annee ;
    public  $image ;
    const GENRES = [
        'Rock Classique', 
        'Pop / Variété Internationale',
        'Hip-Hop / Rap Français', 
        'Electro / Dance',
    ];
   

   protected function rules(): array
    {
        return [
            'titre' => 'required|string|max:255',
            'artiste' => 'required|string|max:255',
            'extract' => 'required|file|mimes:mp3,wav|max:5000', 
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            
            // L'appel de fonction est maintenant autorisé car il est dans une méthode d'instance
            'annee' => 'nullable|integer|min:1900|max:' . (int)date('Y'), 
            
            // La concaténation de constante est correcte, mais l'utilisation dans une méthode est nécessaire.
            'genre' => 'required|string|in:' . implode(',', self::GENRES),
        ];
    }
    public function saveMusique()
    {
        $this->validate();
        $extractPath = $this->extract->store('musique_extraits', 'public');
        $imagePath = $this->image ? $this->image->store('musique_images', 'public') : null;
        Musique::create([
            'titre' => $this->titre,
            'artiste' => $this->artiste,
            'extract' => $extractPath,
            'image' => $imagePath,
            'annee' => $this->annee,
            'genre' => $this->genre,
        ]);

        session()->flash('message', "La musique '{$this->titre}' a été ajoutée avec succès !");
        
        // Utiliser reset() avec les propriétés si vous ne voulez pas effacer le message flash
        $this->reset(['titre', 'artiste', 'extract', 'image', 'annee', 'genre']); 
    }
    public function getGenresProperty()
{
    return self::GENRES;
}

    public function render()
    {
        // Récupérer les dernières musiques pour les afficher dans le tableau (comme discuté)
        $musiques = Musique::orderBy('created_at', 'desc')->limit(20)->get();
        
        return view('livewire.admin-music-manager', [
            'musiques' => $musiques,
        ]);
    }
}