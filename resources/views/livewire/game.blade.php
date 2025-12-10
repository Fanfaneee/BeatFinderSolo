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
    
    class="p-6 max-w-7xl mx-auto min-h-screen" wire:poll.1s="tick"> 
    
    <h1 class="text-3xl font-extrabold text-indigo-700 text-center mb-6">
        Blind Test Solo - Manche {{ $mancheActuelle }} / {{ $jeu->nombre_manches }}
    </h1>

    <div class="grid grid-cols-4 gap-6">
        
        {{-- COLONNE 1, 2, 3 : JEU ACTUEL (LECTURE / RÉPONSE / RÉVÉLATION) --}}
        <div class="col-span-3 bg-white p-6 rounded-xl shadow-2xl border border-gray-200">
            
            {{-- 🔥 CONTROLEUR DE VOLUME --}}
            <div class="flex justify-end mb-4 -mt-3">
                <div class="flex items-center space-x-2 bg-gray-100 p-2 rounded-lg shadow-inner">
                    
                    {{-- Bouton Mute/Unmute --}}
                    <button @click="toggleMute()" class="text-gray-600 hover:text-indigo-600 p-1">
                        {{-- Icône Volume Up (Visible si pas muted) --}}
                        <svg x-show="!isMuted && audioVolume > 0.01" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-volume-up-fill" viewBox="0 0 16 16">
                            <path d="M11.536 14.01A8.47 8.47 0 0 0 14.026 8a8.47 8.47 0 0 0-2.49-6.01l-.708.707A7.48 7.48 0 0 1 13.025 8c0 2.071-.84 3.946-2.197 5.303z"/>
                            <path d="M10.121 12.596A6.48 6.48 0 0 0 12.025 8a6.48 6.48 0 0 0-1.904-4.596l-.707.707A5.48 5.48 0 0 1 11.025 8a5.48 5.48 0 0 1-1.61 3.89z"/>
                            <path d="M10.025 8a4.5 4.5 0 0 1-1.318 3.182L8 10.475A3.5 3.5 0 0 0 9.025 8c0-.966-.392-1.841-1.025-2.475l.707-.707A4.5 4.5 0 0 1 10.025 8M7 4a.5.5 0 0 0-.812-.39L3.825 5.5H1.5A.5.5 0 0 0 1 6v4a.5.5 0 0 0 .5.5h2.325l2.363 1.89A.5.5 0 0 0 7 12zM4.312 6.39 6 5.04v5.92L4.312 9.61A.5.5 0 0 0 4 9.5H2v-3h2a.5.5 0 0 0 .312-.11"/>
                        </svg>
                        {{-- Icône Volume Off (Visible si muted) --}}
                        <svg x-show="isMuted || audioVolume <= 0.01" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-volume-mute-fill" viewBox="0 0 16 16">
                            <path d="M11.95 6.096A6.47 6.47 0 0 0 14.026 8c0 1.62-.647 3.093-1.684 4.096L13.1 13.593A7.47 7.47 0 0 1 14.026 8c0-2.071-.84-3.946-2.197-5.303l-.707.707z"/>
                            <path d="M10.121 12.596A6.48 6.48 0 0 0 12.025 8a6.48 6.48 0 0 0-1.904-4.596l-.707.707A5.48 5.48 0 0 1 11.025 8a5.48 5.48 0 0 1-1.61 3.89z"/>
                            <path d="M10.025 8a4.5 4.5 0 0 1-1.318 3.182L8 10.475A3.5 3.5 0 0 0 9.025 8c0-.966-.392-1.841-1.025-2.475l.707-.707A4.5 4.5 0 0 1 10.025 8M7 4a.5.5 0 0 0-.812-.39L3.825 5.5H1.5A.5.5 0 0 0 1 6v4a.5.5 0 0 0 .5.5h2.325l2.363 1.89A.5.5 0 0 0 7 12zM4.312 6.39 6 5.04v5.92L4.312 9.61A.5.5 0 0 0 4 9.5H2v-3h2a.5.5 0 0 0 .312-.11"/>
                        </svg>
                    </button>
                    
                    {{-- Slider de Volume --}}
                    <input 
                        type="range" 
                        min="0" 
                        max="1" 
                        step="0.1" 
                        x-model.number="audioVolume" 
                        @input="isMuted = (audioVolume === 0)"
                        class="w-24 h-1 bg-gray-300 rounded-lg appearance-none cursor-pointer range-sm accent-indigo-600"
                    >
                    
                </div>
            </div>
            
            <div class="bg-indigo-50 p-4 rounded-lg text-center mb-6 border-b-4 border-indigo-500">
                <p class="text-xl font-semibold text-gray-700">Score total : <span class="text-3xl font-bold text-indigo-700">{{ $score }}</span> pts</p>
            </div>
            
            @if ($roundStatus === 'playing' || $roundStatus === 'revealed')
                
                <div class="text-center mb-6">
                    <p class="text-6xl font-extrabold {{ $roundStatus === 'playing' ? 'text-red-600 animate-pulse' : 'text-green-600' }}">
                        {{ $timeRemaining }}s
                    </p>
                    <p class="text-xl text-gray-600">
                        @if ($roundStatus === 'playing')
                            🎧 Écoutez et répondez !
                        @else
                            ➡️ Prochaine manche...
                        @endif
                    </p>
                </div>

                {{-- LECTEUR AUDIO PENDANT LA LECTURE (wire:key pour le re-rendu unique) --}}
                @if ($currentMusic)
                    <div class="flex justify-center items-center mb-8" x-data="{ playing: false }">
                        <audio 
                            id="game-audio"
                            wire:key="audio-{{ $currentMusic->id }}"
                            src="{{ asset('storage/' . $currentMusic->extract) }}" 
                            controls 
                            class="w-full max-w-sm"
                            loop
                            x-init="$el.volume = audioVolume; $el.muted = isMuted; $el.play().catch(e => console.log('Autoplay bloqué, nécessite interaction utilisateur.'));"
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
                            class="w-full p-3 border-2 border-yellow-300 rounded-lg focus:ring-yellow-500 focus:border-yellow-500"
                        >
                        <button 
                            type="submit"
                            @if($hasFoundFullAnswer) disabled @endif
                            class="w-full py-3 bg-yellow-600 text-white font-bold rounded-lg hover:bg-yellow-700 disabled:opacity-50 transition"
                        >
                            Soumettre la Réponse
                        </button>
                    </form>

                    @if ($answerMessage)
                        <p class="mt-4 text-center font-semibold {{ str_contains($answerMessage, 'Mauvaise') ? 'text-red-500' : 'text-green-600' }}">
                            {{ $answerMessage }}
                        </p>
                    @endif
                @endif
                
                {{-- Nous avons supprimé le bloc révélé complet car le journal le remplace --}}

            @elseif ($roundStatus === 'finished')

                <div class="text-center p-6 bg-green-100 rounded-lg border-4 border-green-500">
                    <h2 class="text-4xl font-extrabold text-green-800 mb-3">🎉 PARTIE TERMINÉE !</h2>
                    <p class="text-2xl font-semibold">Score Final: {{ $score }} pts</p>
                    <p class="text-lg mt-4 text-gray-600">Félicitations pour votre performance !</p>
                    <a href="{{ route('home') }}" class="mt-6 inline-block py-2 px-6 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-700 transition">
                        Retour à l'Accueil
                    </a>
                </div>

            @else
                <p class="text-center text-gray-500">Chargement de la partie...</p>
            @endif

        </div>

        {{-- COLONNE 4 : HISTORIQUE DES CHANSONS RÉVÉLÉES --}}
        <div class="col-span-1 bg-gray-50 p-4 rounded-xl shadow-inner border border-gray-300 max-h-[70vh] overflow-y-auto">
            <h2 class="text-xl font-semibold mb-4 text-gray-700 sticky top-0 bg-gray-50 pb-2 border-b">
                Journal des Manches
            </h2>
            
            <ul class="space-y-4">
                @forelse (array_reverse($revealedMusics) as $item)
                    <li class="p-3 border-l-4 {{ $item['score_gagne'] > 0 ? 'border-green-500 bg-green-50' : 'border-gray-400 bg-gray-100' }} rounded-md shadow-sm flex space-x-3 items-start">
                        
                        @if ($item['image'])
                            <img src="{{ $item['image'] }}" 
                                alt="Pochette {{ $item['titre'] }}" 
                                class="h-10 w-10 object-cover rounded-md flex-shrink-0 mt-1">
                        @endif
                        
                        <div>
                            <p class="text-xs text-gray-500 font-bold">MANCHE {{ $item['manche'] }}</p>
                            <p class="font-medium text-gray-900 leading-tight">{{ $item['titre'] }}</p>
                            <p class="text-sm text-gray-600">par {{ $item['artiste'] }}</p>
                            
                            @if ($item['score_gagne'] > 0)
                                <p class="text-sm font-bold text-green-700">+{{ $item['score_gagne'] }} pts</p>
                            @endif
                        </div>
                    </li>
                @empty
                    <p class="text-sm text-gray-500">Aucune manche terminée.</p>
                @endforelse
            </ul>
        </div>
        
    </div>
</div>