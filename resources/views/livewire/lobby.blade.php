<div class="flex items-start justify-center p-6 bg-gray-50 min-h-screen">
    <div class="w-full max-w-4xl">
        
        {{-- ÉTAPE 1: SÉLECTION DU GENRE --}}
        @if (!$selectedGenre)
            <h1 class="text-3xl font-extrabold text-center text-indigo-700 mb-8">
                Sélectionnez votre Mode de Jeu
            </h1>
            
            {{-- NOUVEAU CONTENEUR PLUS GRAND ET PLUS LARGE --}}
            <div class="relative w-full h-80 mx-auto max-w-3xl">
                
                {{-- Corps du Carrousel (Masque le débordement) --}}
                <div class="carousel-body h-full relative overflow-hidden rounded-xl shadow-2xl border-2 border-indigo-100">
                    
                    {{-- Wrapper des slides (glisse horizontalement) --}}
                    <div 
                        class="h-full flex transition-transform duration-500 ease-in-out"
                        style="width: {{ count(App\Livewire\Lobby::GENRES_CHOIX) * 100 }}%; 
                               transform: translateX(-{{ $currentSlideIndex * (100 / count(App\Livewire\Lobby::GENRES_CHOIX)) }}%);"
                    >
                        
                        @foreach (App\Livewire\Lobby::GENRES_CHOIX as $index => $genre)
                            {{-- Chaque slide prend une fraction égale de la largeur totale du wrapper --}}
                            <div 
                                wire:click="selectGenre('{{ $genre }}')"
                                {{-- La largeur est 1 / Nombre de genres --}}
                                style="width: calc(100% / {{ count(App\Livewire\Lobby::GENRES_CHOIX) }});"
                                class="flex-shrink-0 p-8 flex flex-col h-full justify-center items-center cursor-pointer bg-white transition duration-300 transform hover:scale-[1.01]"
                            >
                                <p class="text-3xl font-extrabold text-indigo-700 mb-2">{{ $genre }}</p>
                                <p class="text-base text-gray-600 mt-2">Cliquez pour lancer la partie</p>
                            </div>
                        @endforeach
                        
                    </div>
                    
                </div>
                
                {{-- Indicateur de position (inchangé) --}}
                <div class="absolute bottom-4 left-0 right-0 flex justify-center space-x-2 z-20">
                    @foreach (App\Livewire\Lobby::GENRES_CHOIX as $index => $genre)
                        <span 
                            wire:click="$set('currentSlideIndex', {{ $index }})"
                            class="block w-2 h-2 rounded-full cursor-pointer transition duration-300 
                                   {{ $index === $currentSlideIndex ? 'bg-indigo-700 w-4' : 'bg-gray-400' }}"
                            aria-label="Aller à la slide {{ $index + 1 }}"
                        ></span>
                    @endforeach
                </div>

                {{-- Bouton Précédent (inchangé) --}}
                <button 
                    type="button" 
                    wire:click="prevSlide" 
                    @if ($currentSlideIndex === 0) disabled @endif
                    class="absolute top-1/2 -translate-y-1/2 left-4 z-20 size-10 bg-indigo-500/80 hover:bg-indigo-600 text-white flex items-center justify-center rounded-full shadow-xl transition duration-200 disabled:opacity-30 disabled:cursor-not-allowed"
                >
                     <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/></svg>
                    <span class="sr-only">Précédent</span>
                </button>

                {{-- Bouton Suivant (inchangé) --}}
                <button 
                    type="button" 
                    wire:click="nextSlide" 
                    @if ($currentSlideIndex === count(App\Livewire\Lobby::GENRES_CHOIX) - 1) disabled @endif
                    class="absolute top-1/2 -translate-y-1/2 right-4 z-20 size-10 bg-indigo-500/80 hover:bg-indigo-600 text-white flex items-center justify-center rounded-full shadow-xl transition duration-200 disabled:opacity-30 disabled:cursor-not-allowed"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 0 0 .708L10.293 8l-5.647 5.646a.5.5 0 0 0 .708.708l6-6a.5.5 0 0 0 0-.708l-6-6a.5.5 0 0 0-.708 0z"/></svg>
                    <span class="sr-only">Suivant</span>
                </button>
            </div>
            
        {{-- ÉTAPE 2: CONFIGURATION ET LANCEMENT (Reste inchangé) --}}
        @elseif ($gameId)
            {{-- Transition vers Game --}}
            @livewire('game', ['gameId' => $gameId]) 
            
        @else
            <div class="w-full max-w-lg mx-auto bg-white p-8 rounded-xl shadow-2xl border border-indigo-100">
                
                <div class="flex items-center mb-6">
                    <button 
                        wire:click="resetState" 
                        class="p-2 pointer mr-4 text-gray-600 hover:text-indigo-600 transition duration-150 rounded-full bg-gray-100 hover:bg-gray-200 focus:outline-none"
                        title="Retour à la sélection des genres"
                    >
                        {{-- Icône SVG (flèche gauche) --}}
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8"/>
                        </svg>
                    </button>
                    <h1 class="text-2xl font-bold">
                        Configuration : <span class="text-green-600">{{ $selectedGenre }}</span>
                    </h1>
                </div>
                
                {{-- Formulaire de configuration --}}
                <form wire:submit.prevent="createGame" class="space-y-6">
                    
                    {{-- NOM DU JEU --}}
                    <div>
                        <label for="gameName" class="block text-sm font-medium text-gray-700 mb-1">Nom de la Partie</label>
                        <input wire:model="gameName" type="text" id="gameName" class="w-full p-2 border rounded-lg">
                        @error('gameName') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
        
                    {{-- NOMBRE DE MANCHES --}}
                    <div>
                        <label for="nombreManches" class="block text-sm font-medium text-gray-700 mb-1">Nombre de Manches</label>
                        <input wire:model="nombreManches" type="number" id="nombreManches" min="3" max="50" class="w-full p-2 border rounded-lg">
                        @error('nombreManches') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    
                    <button type="submit" class="w-full py-3 bg-green-600 text-white font-bold rounded-lg hover:bg-green-700">
                        Lancer la Partie {{ $selectedGenre }}
                    </button>
                    
                </form>
            </div>
        @endif
    </div>
</div>