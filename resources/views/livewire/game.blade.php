<div x-data="{ 
    // Sauvegarde du volume dans localStorage (défaut: 50%)
    audioVolume: $persist(0.5).as('globalAudioVolume'), 
    isMuted: $persist(false).as('globalMuteState'), 

    // Fonction pour appliquer le volume à tous les éléments audio de la page
    syncVolume() {
        document.querySelectorAll('audio').forEach(audio => {
            audio.volume = this.audioVolume;
            audio.muted = this.isMuted;
        });
    },
    
    toggleMute() {
        this.isMuted = !this.isMuted;
        this.syncVolume();
    }
}" 
x-init="syncVolume();" 
x-effect="syncVolume();"
class="min-h-screen bg-wtt-noise-gradient" wire:poll.1s="tick"> 
    
    @livewire('score-saver')
    
    <div class="p-6 max-w-7xl mx-auto">
        
        {{-- 1. EN-TÊTE DE JEU & CONTRÔLES DE VOLUME (Le nom de la partie / manche et le volume - ne bouge pas) --}}
        <div class="flex justify-between items-center  p-4 rounded-xl   mb-6">
            
            {{-- TITRE DE LA PARTIE / MANCHE --}}
            <h1 class="text-3xl font-extrabold text-bleu font-cherry">
                {{ $jeu->name }} - Manche {{ $mancheActuelle }} / {{ $jeu->nombre_manches }}
            </h1>

            {{-- CONTRÔLEUR DE VOLUME --}}
            <div class="flex items-center space-x-2  p-2 rounded-lg ">
                <button @click="toggleMute()" class="text-blue hover:text-indigo-600 p-1">
                    <svg x-show="!isMuted && audioVolume > 0.01" width="20" height="20" viewBox="0 -1.5 31 31" fill="currentColor" class="bi bi-volume-up-fill" xmlns="http://www.w3.org/2000/svg"><path d="M277,571.015 L277,573.068 C282.872,574.199 287,578.988 287,585 C287,590.978 283,595.609 277,596.932 L277,598.986 C283.776,597.994 289,592.143 289,585 C289,577.857 283.776,572.006 277,571.015 L277,571.015 Z M272,573 L265,577.667 L265,592.333 L272,597 C273.104,597 274,596.104 274,595 L274,575 C274,573.896 273.104,573 272,573 L272,573 Z M283,585 C283,581.477 280.388,578.59 277,578.101 L277,580.101 C279.282,580.564 281,582.581 281,585 C281,587.419 279.282,589.436 277,589.899 L277,591.899 C280.388,591.41 283,588.523 283,585 L283,585 Z M258,581 L258,589 C258,590.104 258.896,591 260,591 L263,591 L263,579 L260,579 C258.896,579 258,579.896 258,581 L258,581 Z" id="volume-full" sketch:type="MSShapeGroup" transform="translate(-258.000000, -571.000000)"/> </svg>
                    <svg x-show="isMuted || audioVolume <= 0.01" width="20" height="20" viewBox="0 -3 30 30" fill="currentColor" class="bi bi-volume-mute-fill" xmlns="http://www.w3.org/2000/svg"><path d="M336.444,585 L340.617,580.827 C341.067,580.377 341.109,579.688 340.711,579.289 C340.312,578.891 339.623,578.933 339.173,579.383 L335,583.556 L330.827,579.383 C330.377,578.933 329.688,578.891 329.289,579.289 C328.891,579.688 328.933,580.377 329.383,580.827 L333.556,585 L329.383,589.173 C328.933,589.623 328.891,590.312 329.289,590.711 C329.688,591.109 330.377,591.067 330.827,590.617 L335,586.444 L339.173,590.617 C339.623,591.067 340.312,591.109 340.711,590.711 C341.109,590.312 341.067,589.623 340.617,589.173 L336.444,585 L336.444,585 Z M325,573 L318,577.667 L318,592.333 L325,597 C326.104,597 327,596.104 327,595 L327,575 C327,573.896 326.104,573 325,573 L325,573 Z M311,581 L311,589 C311,590.104 311.896,591 313,591 L316,591 L316,579 L313,579 C311.896,579 311,579.896 311,581 L311,581 Z" id="volume-muted" sketch:type="MSShapeGroup" transform="translate(-311.000000, -573.000000)"/> </svg>
                </button>
                <input type="range" min="0" max="1" step="0.1" x-model.number="audioVolume" @input="isMuted = (audioVolume === 0)" class="w-24 h-1  bg-gray-300 rounded-lg appearance-none cursor-pointer range-sm accent-indigo-600">
            </div>
        </div>
        
        @if ($roundStatus === 'finished')
             {{-- Affichage de fin de partie --}}
            <div class="text-center p-12 rounded-xl  border-green-500 l">
                <h2 class="text-5xl font-extrabold text-green-800 mb-4">PARTIE TERMINÉE !</h2>
                <p class="text-3xl font-semibold">Score Final: {{ $score }} pts</p>
                <p class="text-lg mt-4 text-white">Félicitations pour votre performance !</p>
                <a href="{{ route('home') }}" class="mt-8 inline-block py-3 px-8 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 transition transform hover:scale-105">
                    Retour à l'Accueil
                </a>
            </div>
        
        @elseif ($roundStatus === 'playing' || $roundStatus === 'revealed')

            {{-- 2. ZONE DE RÉPONSE / BOUTON (Pleine largeur) --}}
            <div class=" p-6 rounded-xl ">
                
               {{-- LECTEUR AUDIO --}}
@if ($currentMusic)
    <div class="mt-4 h-14 bg-gradient-to-r from-indigo-300 to-indigo-500/40 rounded-xl overflow-hidden flex items-end p-2 space-x-1 border border-indigo-200 shadow-sm mb-6 backdrop-blur-md">
        
        {{-- Visuel Sonore Moderne --}}
        @for ($i = 0; $i < 30; $i++)
            <div wire:ignore class="rounded-md bg-indigo-600/80 "
                 style="
                    width: 3%;
                    height: {{ rand(40, 95) }}%;
                    animation: soundbar-modern 1.2s ease-in-out infinite alternate;
                    animation-delay: {{ $i * 0.07 }}s;
                 ">
            </div>
        @endfor

        <style>
            @keyframes soundbar-modern {
                0% { 
                    transform: scaleY(0.4); 
                    opacity: 0.7;
                }
                100% { 
                    transform: scaleY(1); 
                    opacity: 1;
                }
            }
        </style>
    </div>

    {{-- Lecteur réel (caché) --}}
    <div class="flex hidden justify-center items-center mb-8" x-data="{ playing: false }">
        <audio 
            id="game-audio"
            wire:key="audio-{{ $currentMusic->id }}-{{ $mancheActuelle }}"
            src="{{ asset('storage/' . $currentMusic->extract) }}" 
            controls 
            class="w-full max-w-sm"
            x-init="
                $el.volume = audioVolume; 
                $el.muted = isMuted; 
                $el.play().catch(e => console.log('Autoplay bloqué.'));
                setTimeout(() => { $el.pause(); }, 15000);
            "
        ></audio>
    </div>
@endif

                
                {{-- Formulaire de Réponse (visible uniquement en phase 'playing') --}}
                @if ($roundStatus === 'playing')
                    <form wire:submit.prevent="submitAnswer" class="space-y-4">
                        <input 
                            wire:model.defer="userAnswer" 
                            type="text" 
                            placeholder="Titre et Artiste..."
                            required
                            @if($hasFoundFullAnswer) disabled @endif
                            class="w-full p-4 text-xl bg-white rounded-xl shadow-lg focus:ring-yellow-500 focus:border-yellow-500 transition duration-150"
                        >
                        <button 
                            type="submit"
                            @if($hasFoundFullAnswer) disabled @endif
                            class="w-full hidden py-4  text-white text-2xl font-extrabold rounded-xl hover:bg-yellow-700 disabled:opacity-50 transition transform hover:scale-[1.01]"
                        >
                            Soumettre la Réponse
                        </button>
                    </form>

                    @if ($answerMessage)
                        <p class="mt-4 text-center text-xl font-bold {{ str_contains($answerMessage, 'Mauvaise') ? 'text-red-500' : 'text-green-600' }}">
                            {{ $answerMessage }}
                        </p>
                    @endif
                @endif

                {{-- AFFICHAGE DE LA RÉPONSE RÉVÉLÉE --}}
                @if ($roundStatus === 'revealed' && $currentMusic)
                    <div class="text-center p-4  rounded-lg mt-4">
                        <p class="text-lg font-semibold text-gray-700">La bonne réponse était :</p>
                        <p class="text-2xl font-bold text-bleu">{{ $currentMusic->titre }} - {{ $currentMusic->artiste }}</p>
                    </div>
                @endif
            </div>

            {{-- 3. MINUTEUR (Pleine largeur) --}}
            <div class="  rounded-xl  text-center mb-6 ">
                <p class="text-6xl font-extrabold {{ $roundStatus === 'playing' ? 'text-red-600 ' : 'text-green-600' }}">
                    {{ $timeRemaining }}s
                </p>
                <p class="text-xl text-gray-600">
                    @if ($roundStatus === 'playing') Temps restant pour répondre ! @else Prochaine manche... @endif
                </p>
            </div>


            {{-- 4. GRILLE DEUX COLONNES : JOURNAL (Gauche) & SCORE (Droite) --}}
            <div class="grid grid-cols-2 gap-6">
                
                {{-- COLONNE GAUCHE : JOURNAL DES MANCHES --}}
<div class="col-span-1 p-6 rounded-xl max-h-[70vh] overflow-y-auto">
    
    {{-- Conteneur du titre --}}
    <div class="text-center">
        <p class="text-3xl font-semibold text-gray-700 mb-2"> Journal des Manches</p>
    </div>
    
    {{-- Contenu du Journal avec Alpine.js --}}
    <div x-data="{ revealedMusics: @js($revealedMusics) }" wire:model="revealedMusics">
        {{-- ✅ RETIRER 'text-center' d'ici --}}
        <ul class="space-y-4"> 
            <template x-for="item in revealedMusics.slice().reverse()" :key="item.manche">
                
                {{-- ITEM LI : Nous allons le centrer dans la colonne --}}
                <li x-transition:enter="transition ease-out duration-300 transform" 
                    x-transition:enter-start="opacity-0 translate-y-4" 
                    x-transition:enter-end="opacity-100 translate-y-0" 
                    
                    {{-- ✅ AJOUTER mx-auto pour centrer la liste item --}}
                    class="p-3 rounded-md flex space-x-3 items-start mx-auto w-11/12" 
                    :class="item.score_gagne > 0 ? 'border-green-500 bg-green-50' : 'border-gray-400 bg-gray-100'"
                >
                    {{-- Le contenu de la liste item (image + texte) --}}
                    <template x-if="item.image">
                        <img :src="item.image" :alt="'Pochette ' + item.titre" class="h-10 w-10 object-cover rounded-md flex-shrink-0 mt-1">
                    </template>
    
                    <div class="flex-grow"> 
                        <p class="text-xs text-gray-500 font-bold" x-text="'MANCHE ' + item.manche"></p>
                        <p class="font-medium text-gray-900 leading-tight" x-text="item.titre"></p>
                        <p class="text-sm text-gray-600" x-text="'par ' + item.artiste"></p>
                        
                        <template x-if="item.score_gagne > 0">
                            <p class="text-sm font-bold text-green-700" x-text="'+' + item.score_gagne + ' pts'"></p>
                        </template>
                    </div>
                </li>
            </template>

            <template x-if="revealedMusics.length === 0">
                <p class="text-sm text-gray-500 text-center">Aucune manche terminée.</p>
            </template>
        </ul>
    </div>
</div>

                {{-- COLONNE DROITE : SCORE ACTUEL --}}
                <div class="col-span-1 p-6 rounded-xl   flex items-center justify-center">
                    <div class="text-center">
                        <p class="text-3xl font-semibold text-gray-700 mb-2">Votre Score Actuel :</p>
                        <p class="text-7xl font-extrabold text-bleu">{{ $score }}</p>
                        <p class="text-2xl text-gray-500 mt-1">points</p>
                    </div>
                </div>

            </div>
        
        @else
            <p class="text-center text-gray-500">Chargement de la partie...</p>
        @endif
        
    </div>
</div>