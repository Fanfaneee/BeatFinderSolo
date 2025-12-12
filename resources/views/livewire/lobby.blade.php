<div class="flex items-start justify-center  p-6 0 min-h-screen">
    <div class="w-full ">  
        
        {{-- ÉTAPE 1: SÉLECTION DU GENRE --}}
        @if (!$selectedGenre)
            <h1 class="text-4xl font tracking-wide border-text font-extrabold text-center text-bleu ">
                Sélectionnez votre Mode de Jeu
            </h1>
            
            {{-- NOUVEAU CONTENEUR PLUS GRAND ET PLUS LARGE --}}
            <div class="relative w-full h-80 mx-auto max-w-3xl">
                
                {{-- Corps du Carrousel (Masque le débordement) --}}
                <div class="carousel-body h-full relative overflow-hidden rounded-xl">
                    
                    {{-- Wrapper des slides (glisse horizontalement) --}}
                    <div 
                        class="h-full flex transition-transform duration-500 ease-in-out"
                        style="width: {{ count(App\Livewire\Lobby::GENRES_CHOIX) * 100 }}%; 
                               transform: translateX(-{{ $currentSlideIndex * (100 / count(App\Livewire\Lobby::GENRES_CHOIX)) }}%);"
                    >
                        
                        @foreach (App\Livewire\Lobby::GENRES_CHOIX as $index => $genre)
                            
                            <div  wire:click="selectGenre('{{ $genre }}')" style="width: calc(100% / {{ count(App\Livewire\Lobby::GENRES_CHOIX) }});" class="flex-shrink-0 p-8 flex flex-col h-full justify-center items-center cursor-pointer ">
                                <div class=" transition duration-300 transform hover:scale-[1.05]">
                                <p class=" text-3xl font-extrabold text-white mb-2">{{ $genre }}</p>
                                <p class="text-base text-center  text-bleu    mt-2">Cliquez pour lancer la partie</p>
                                </div>
                            </div>
                        @endforeach
                        
                    </div>
                    
                </div>
                
                {{-- Indicateur de position (inchangé) --}}
                <div class="absolute bottom-4 left-0 right-0 flex justify-center space-x-2 z-20">
                    @foreach (App\Livewire\Lobby::GENRES_CHOIX as $index => $genre)
                        <span wire:click="$set('currentSlideIndex', {{ $index }})" class="block w-2 h-2 rounded-full cursor-pointer transition duration-300 {{ $index === $currentSlideIndex ? 'bg-indigo-700 w-4' : 'bg-white' }}" aria-label="Aller à la slide {{ $index + 1 }}"></span>
                    @endforeach
                </div>

                {{-- Bouton Précédent (inchangé) --}}
                <button type="button"  wire:click="prevSlide"  @if ($currentSlideIndex === 0)  @endif class="absolute top-1/2 -translate-y-1/2 left-4 z-20 size-10 bg-indigo-500/80 hover:bg-indigo-600 text-white flex items-center justify-center rounded-full shadow-xl transition duration-200">
                     <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/></svg>
                    <span class="sr-only">Précédent</span>
                </button>

                {{-- Bouton Suivant (inchangé) --}}
                <button  type="button"  wire:click="nextSlide"  @if ($currentSlideIndex === count(App\Livewire\Lobby::GENRES_CHOIX) - 1)  @endif class="absolute top-1/2 -translate-y-1/2 right-4 z-20 size-10 bg-indigo-500/80 hover:bg-indigo-600 text-white flex items-center justify-center rounded-full shadow-xl transition duration-200 ">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 0 0 .708L10.293 8l-5.647 5.646a.5.5 0 0 0 .708.708l6-6a.5.5 0 0 0 0-.708l-6-6a.5.5 0 0 0-.708 0z"/></svg>
                    <span class="sr-only">Suivant</span>
                </button>
            </div>
            

<h2 class="text-3xl font-extrabold text-center font text-bleu mt-15 mb-8">
    Meilleurs Scores par Catégorie
</h2>

<div class="mx-70 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach ($allPodiums as $categorie => $podiumScores)
        <div class="bg-bleu p-6 rounded-xl shadow-lg h-full flex flex-col ">
            <h3 class="text-xl font-bold text-white mb-4 border-b pb-2">
                {{-- Afficher Global si la catégorie est Global --}}
                Top 5 : {{ $categorie === 'Global' ? 'Global (Toutes catégories)' : $categorie }}
            </h3>
                        
                        @if ($podiumScores->isEmpty())
                            <p class="text-center text-sm text-white mt-4 flex-grow flex items-center justify-center">
                                Aucun score enregistré.
                            </p>
                        @else
                            <ol class=" list-decimal list-inside">
                                @foreach ($podiumScores as $index => $scoreRecord)
                                    @php
                                        // Vérifie si l'utilisateur est connecté et si son ID correspond au record
                                        $isCurrentUser = $currentUserId && $scoreRecord->user_id === $currentUserId;
                                        
                                        $rankClass = '';
                                        if ($index === 0) $rankClass = 'border-yellow-500 ';
                                        elseif ($index === 1) $rankClass = 'border-gray-400';
                                        elseif ($index === 2) $rankClass = 'border-amber-700 ';
                                        else $rankClass = 'border-gray-200 ';
                                    @endphp

                                    <li class=" flex justify-between items-center ">
                                        
                                        <div class="flex items-center space-x-2">
                                            <span class="font-bold w-4 text-white   text-center">{{ $index + 1 }}.</span>
                                            
                                            <span class="font-semibold {{ $isCurrentUser ? 'text-indigo-700 underline' : 'text-white' }}">
                                                {{ $scoreRecord->user->username ?? 'Utilisateur inconnu' }}
                                                @if ($isCurrentUser) (Vous) @endif
                                            </span>
                                        </div>

                                        <span class="font-extrabold text-lg {{ $isCurrentUser ? 'text-white' : 'text-white' }}">
                                            {{ number_format($scoreRecord->score, 0, ',', ' ') }} pts
                                        </span>
                                    </li>
                                @endforeach
                            </ol>
                        @endif
                    </div>
                @endforeach
            </div>
        {{-- ÉTAPE 2: CONFIGURATION ET LANCEMENT --}}
@elseif ($gameId)
    {{-- Transition vers Game --}}
    @livewire('game', ['gameId' => $gameId]) 
    
@else
   
    <div class="w-full max-w-lg mx-auto bg-bleu p-8 rounded-2xl shadow-2xl border border-white/50 backdrop-blur-sm">
        
        <div class="flex items-center mb-6">
            {{-- Bouton Retour --}}
            <button wire:click="resetState" class="p-2 mr-4 cursor-pointer text-gray-600 hover:text-bleu transition duration-150 rounded-full bg-gray-100 hover:bg-gray-200 focus:outline-none" title="Retour à la sélection des genres" >
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8"/>
                </svg>
            </button>
            <h1 class="text-2xl text-white font-bold">
                Configuration : <span class="text-white">{{ $selectedGenre }}</span>
            </h1>
        </div>
        
        {{-- Formulaire de configuration --}}
        <form wire:submit.prevent="createGame" class="space-y-6">
            
            {{-- NOM DU JEU --}}
            <div>
                <label for="gameName" class="block text-sm font-semibold text-white mb-2">Nom de la Partie</label>
                <input 
                    wire:model="gameName" 
                    type="text" 
                    id="gameName" 
                    placeholder="Entrez un nom de partie..."
                    class="w-full p-3 border-2 couleur-bouton bg-white rounded-xl shadow-inner 
                           focus:border-bleu focus:ring-bleu focus:ring-1 
                           transition duration-200 text-gray-800"
                >
                @error('gameName') <p class="mt-1 text-sm text-red-600 font-medium">{{ $message }}</p> @enderror
            </div>

            {{-- NOMBRE DE MANCHES --}}
            <div>
                <label for="nombreManches" class="block text-sm font-semibold text-white mb-2">Nombre de Manches (3 à 50)</label>
                <input 
                    wire:model="nombreManches" 
                    type="number" 
                    id="nombreManches" 
                    min="3" 
                    max="50" 
                    class="w-full p-3 border-2 couleur-bouton bg-white rounded-xl shadow-inner 
                           focus:border-bleu focus:ring-bleu focus:ring-1 
                           transition duration-200 text-gray-800"
                >
                @error('nombreManches') <p class="mt-1 text-sm text-red-600 font-medium">{{ $message }}</p> @enderror
            </div>
            
            {{-- BOUTON DE LANCEMENT --}}
            <button 
                type="submit" 
                class="w-full py-4 px-4 
                       bg-white text-bleu 
                       text-xl font-extrabold uppercase font-cherry
                       rounded-xl shadow-lg 
                       hover:bg-vert hover:text-bleu transition 
                       duration-300 transform hover:scale-[1.05] cursor pointer "
            >
                Lancer la Partie
            </button>
            
        </form>
    </div>
@endif
    </div>
</div>