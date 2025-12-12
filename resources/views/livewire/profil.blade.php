<div class="flex items-start justify-center p-6  min-h-screen">
    <div class="w-full max-w-xl p-8 rounded-xl ">
        @if ($user)
            <h1 class="text-3xl font-extrabold text-center text-bleu font mb-8">
                Mon Profil
            </h1>
            <div class="flex flex-col items-center space-y-6">
                <div class="flex  items-center space-y-4">
                {{-- Affichage de l'Avatar --}}
                <div class="relative w-32 mr-5 h-32">
                    <img src="{{ $this->getAvatarUrl() }}"  alt="Avatar de {{ $user->username }}"  class="w-full h-full rounded-full object-cover border-4 border-indigo-500 shadow-lg">
                    {{-- Badge pour admin  --}}
                    @if ($user->is_admin)
                        <span class="absolute bottom-0 right-0 p-1 bg-red-500 rounded-full border-2 border-white" title="Administrateur"></span>
                    @endif
                </div>
                {{-- Nom d'utilisateur --}}
                <div class="text-center">
                    <p class="text-4xl font-black text-bleu">{{ $user->username }}</p>
                    @if ($user->is_admin)
                        <span class="inline-block px-3 py-1 text-xs font-semibold uppercase tracking-wider text-white bg-red-500 rounded-full mt-1">Admin</span>
                    @endif
                </div>
                </div>
                <hr class="w-full border-t border-gray-200">
                {{-- Informations et Statistiques --}}
                <div class="w-full grid grid-cols-2 gap-4 text-center">
                    <div class="bg-bleu p-4 rounded-lg shadow-sm">
                        <p class="text-xs uppercase font-medium text-white">Parties Jouées</p>
                        <p class="text-2xl font-bold text-white">{{ number_format($gamesPlayedCount, 0, ',', ' ') }}</p> 
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow-sm">
                        <p class="text-xs uppercase font-medium text-bleu">Meilleur Score Absolu</p>
                        {{-- Utilisation de la condition pour éviter l'erreur TypeError --}}
                        <p class="text-2xl font-bold text-bleu">
                            @if (is_numeric($absoluteBestScore))
                                {{ number_format($absoluteBestScore, 0, ',', ' ') }} pts
                            @else
                                {{ $absoluteBestScore }}
                            @endif
                        </p>
                    </div>
                </div>
            </div> 
            
            <hr class="w-full border-t border-gray-200 mt-8 mb-6">
            
            <h2 class="text-2xl font-bold text-center text-bleu mb-6">
                Meilleurs Scores par Catégorie
            </h2>

            @if ($meilleursScores->isEmpty())
                <p class="text-center text-white">Aucune partie enregistrée pour le moment. Jouez pour établir vos records !</p>
            @else
                
                {{-- DÉFINITION DES SCORES POUR LE PODIUM ET LA LISTE --}}
                @php
                    $topScores = $meilleursScores->take(3);
                    $remainingScores = $meilleursScores->skip(3);
                @endphp

    <div class="flex justify-center items-end space-x-4 mb-8">
        @php
            $podiumStyles = [
                1 => 'background-color:#0600d5', 
                2 => 'background-color:#0064ff',
                3 => 'background-color:#00c3ff', 
            ];
        @endphp
        
        {{-- 2ÈME PLACE --}}
        @if (isset($topScores[1]))
            @include('livewire.partials.podium-card', [
                'scoreRecord' => $topScores[1], 
                'rank' => 2, 
                'color' => $podiumStyles[2], 
                'height' => 'h-28'
            ])
        @endif

        {{-- 1ÈRE PLACE --}}
        @if (isset($topScores[0]))
            @include('livewire.partials.podium-card', [
                'scoreRecord' => $topScores[0], 
                'rank' => 1, 
                'color' => $podiumStyles[1],
                'height' => 'h-36'
            ])
        @endif

        {{-- 3ÈME PLACE --}}
        @if (isset($topScores[2]))
            @include('livewire.partials.podium-card', [
                'scoreRecord' => $topScores[2], 
                'rank' => 3, 
                'color' => $podiumStyles[3], 
                'height' => 'h-20'
            ])
        @endif
    </div>

                {{-- LISTE DES SCORES RESTANTS --}}
                @if ($remainingScores->isNotEmpty())
                    <h3 class="text-lg font-semibold text-gray-700 mt-6 mb-3 border-t pt-4">Autres Scores</h3>
                    <div class="space-y-2">
                        @foreach ($remainingScores as $index => $scoreRecord)
                            <div class="flex justify-between items-center p-3 rounded-lg bg-white border border-gray-200 shadow-sm">
                                
                                {{-- Catégorie --}}
                                <span class="text-base font-medium text-indigo-800 flex-grow">
                                    {{ $scoreRecord->categorie }}
                                </span>
                                
                                {{-- Score --}}
                                <span class="text-lg font-bold text-gray-900 ml-4">
                                    {{ number_format($scoreRecord->score, 0, ',', ' ') }} pts
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
                
            @endif

        @else
            <p class="text-center text-red-500">Vous devez être connecté pour voir cette page.</p>
        @endif

    </div>
</div>