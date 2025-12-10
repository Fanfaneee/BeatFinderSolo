<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold mb-6 text-indigo-700">🎸 Gestion du Catalogue Musique</h1>

    {{-- Message de Succès --}}
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- COLONNE 1 : FORMULAIRE D'AJOUT --}}
        <div class="lg:col-span-1 bg-white p-6 shadow-xl rounded-lg border border-gray-200">
            <h2 class="text-xl font-semibold mb-4 border-b pb-2">Ajouter une Nouvelle Musique</h2>

            <form wire:submit.prevent="saveMusique" class="space-y-4">
                
                {{-- TITRE --}}
                <div>
                    <label for="titre" class="block text-sm font-medium text-gray-700">Titre</label>
                    <input wire:model.defer="titre" type="text" id="titre" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                    @error('titre') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- ARTISTE --}}
                <div>
                    <label for="artiste" class="block text-sm font-medium text-gray-700">Artiste</label>
                    <input wire:model.defer="artiste" type="text" id="artiste" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                    @error('artiste') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                {{-- ANNÉE --}}
                <div>
                    <label for="annee" class="block text-sm font-medium text-gray-700">Année</label>
                    <input wire:model.defer="annee" type="number" id="annee" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                    @error('annee') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>  
                {{-- GENRE --}}
                <div>
                    <label for="genre" class="block text-sm font-medium text-gray-700">Genre</label>
                    
                    <select wire:model.defer="genre" id="genre" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                        <option value="">-- Choisir un genre --</option>
                        @foreach ($this->genres as $g)
                            <option value="{{ $g }}">{{ $g }}</option>
                        @endforeach
                    </select>
                    
                    @error('genre') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div x-data="{ isUploadingImage: false, progressImage: 0 }" 
            x-on:livewire-upload-start.only="isUploadingImage = true"
            x-on:livewire-upload-finish.only="isUploadingImage = false"
            x-on:livewire-upload-error.only="isUploadingImage = false"
            x-on:livewire-upload-progress.only="progressImage = $event.detail.progress">
            
            <label for="image" class="block text-sm font-medium text-gray-700">Image (Pochette) (.jpg, .png, .gif)</label>
            
            {{-- Le champ d'input est de type file --}}
            <input wire:model="image" type="file" id="image" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
            
            @error('image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror

            {{-- Indicateur de Progression pour l'Image --}}
            <div x-show="isUploadingImage" class="mt-2">
                <progress max="100" x-bind:value="progressImage" class="w-full h-2 rounded-full bg-indigo-200">
                    <span x-text="progressImage + '%'"></span>
                </progress>
                <p class="text-xs text-gray-500" x-text="'Téléversement image en cours: ' + progressImage + '%'"></p>
            </div>
        </div>

                {{-- EXTRAIT (Fichier Audio) --}}
                <div x-data="{ isUploading: false, progress: 0 }" 
                     x-on:livewire-upload-start="isUploading = true"
                     x-on:livewire-upload-finish="isUploading = false"
                     x-on:livewire-upload-error="isUploading = false"
                     x-on:livewire-upload-progress="progress = $event.detail.progress">
                    
                    <label for="extract" class="block text-sm font-medium text-gray-700">Fichier Extrait Audio (.mp3, .wav)</label>
                    
                    {{-- Le champ d'input est de type file --}}
                    <input wire:model="extract" type="file" id="extract" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                    
                    @error('extract') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror

                    {{-- Indicateur de Progression (visible pendant le téléversement) --}}
                    <div x-show="isUploading" class="mt-2">
                        <progress max="100" x-bind:value="progress" class="w-full h-2 rounded-full bg-gray-200">
                            <span x-text="progress + '%'"></span>
                        </progress>
                        <p class="text-xs text-gray-500" x-text="'Téléversement en cours: ' + progress + '%'"></p>
                    </div>
                </div>

               

                <button type="submit" 
                        class="w-full py-2 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-md shadow-md transition"
                        wire:loading.remove>
                    Enregistrer la Musique
                </button>

                <button type="button" disabled 
                        class="w-full py-2 px-4 bg-indigo-400 text-white font-medium rounded-md shadow-md"
                        wire:loading.delay.short>
                    Téléversement en cours...
                </button>
            </form>
        </div>

        {{-- COLONNE 2 & 3 : LISTE DES DERNIÈRES MUSIQUES --}}
        <div class="lg:col-span-2 bg-white p-6 shadow-xl rounded-lg border border-gray-200">
            <h2 class="text-xl font-semibold mb-4 border-b pb-2">20 Dernières Musiques Ajoutées</h2>
            
            @if ($musiques->isEmpty())
                <p class="text-gray-500">Aucune musique n'a été ajoutée pour le moment.</p>
            @else
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Titre / Artiste</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Année</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Genre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Extrait</th>
                        </tr>
                    </thead>
                    <div class="lg:col-span-2 bg-white p-6 shadow-xl rounded-lg border border-gray-200">
    <tbody class="bg-white divide-y divide-gray-200">
        @foreach ($musiques as $musique)
            <tr>
                 <td class="px-6 py-4 whitespace-nowrap">
                    @php
                        // Conversion du chemin de stockage en URL publique
                        $publicImageUrl = $musique->image ? Storage::url($musique->image) : asset('chemin/vers/image_par_defaut.png');
                    @endphp
                    
                    <img src="{{ $publicImageUrl }}" 
                        alt="Image de {{ $musique->titre }}" 
                        class="h-12 w-12 object-cover rounded-md">
                        </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{{ $musique->titre }}</div>
                    <div class="text-xs text-gray-500">{{ $musique->artiste }}</div>
                </td>
               {{-- Colonne Année --}}
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $musique->annee }}
                </td>

                {{-- Colonne Genre --}}
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ $musique->genre }}
                </td>
                

                <td class="px-6 py-4 whitespace-nowrap text-sm max-w-xs">
                    @php
                        // Conversion du chemin de stockage en URL publique
                        $publicUrl = Storage::url($musique->extract); 
                    @endphp
                    
                    {{-- Balise audio HTML5 avec les contrôles standards --}}
                    {{-- 'controls' affiche la lecture/pause, volume, progression --}}
                    <audio controls class="w-full max-w-[200px] h-8">
                        <source src="{{ $publicUrl }}" type="audio/mpeg">
                        Votre navigateur ne supporte pas la lecture de l'audio.
                    </audio>

                    {{-- Optionnel: Si vous voulez garder un lien de téléchargement --}}
                    {{-- <a href="{{ $publicUrl }}" download class="text-xs text-indigo-500 hover:underline mt-1 block">Télécharger</a> --}}
                </td>
           
                
            </tr>
        @endforeach
    </tbody>
</table>
            @endif
        </div>
    </div>
</div>