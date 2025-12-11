<div class="flex items-start justify-center p-6 bg-gray-50 min-h-screen">
    <div class="w-full max-w-xl bg-white p-8 rounded-xl shadow-2xl border border-indigo-100">
        
        @if ($user)
            <h1 class="text-3xl font-extrabold text-center text-indigo-700 mb-8">
                Mon Profil
            </h1>

            <div class="flex flex-col items-center space-y-6">
                
                {{-- Affichage de l'Avatar --}}
                <div class="relative w-32 h-32">
                    <img 
                        src="{{ $this->getAvatarUrl() }}" 
                        alt="Avatar de {{ $user->username }}" 
                        class="w-full h-full rounded-full object-cover border-4 border-indigo-500 shadow-lg"
                    >
                    {{-- Badge pour l'état (Optionnel) --}}
                    @if ($user->is_admin)
                        <span class="absolute bottom-0 right-0 p-1 bg-red-500 rounded-full border-2 border-white" title="Administrateur"></span>
                    @endif
                </div>

                {{-- Nom d'utilisateur --}}
                <div class="text-center">
                    <p class="text-4xl font-black text-gray-900">{{ $user->username }}</p>
                    
                    @if ($user->is_admin)
                        <span class="inline-block px-3 py-1 text-xs font-semibold uppercase tracking-wider text-white bg-red-500 rounded-full mt-1">Admin</span>
                    @endif
                </div>

                <hr class="w-full border-t border-gray-200">

                {{-- Informations et Statistiques (Ajouté pour la complétude) --}}
                <div class="w-full grid grid-cols-2 gap-4 text-center">
                    
                    <div class="bg-indigo-50 p-4 rounded-lg shadow-sm">
                        <p class="text-xs uppercase font-medium text-indigo-600">Parties Jouées</p>
                        {{-- Utilisation de la propriété calculée --}}
                        <p class="text-2xl font-bold text-gray-800">{{ $gamesPlayedCount }}</p> 
                    </div>

                    <div class="bg-green-50 p-4 rounded-lg shadow-sm">
                        <p class="text-xs uppercase font-medium text-green-600">Meilleur Score Absolu</p>
                        {{-- Utilisation de la propriété calculée --}}
                        <p class="text-2xl font-bold text-gray-800">{{ number_format($absoluteBestScore, 0, ',', ' ') }}</p>
                    </div>
                </div>

                {{-- Lien pour modifier (future fonctionnalité) --}}
                <a href="#" class="mt-6 w-full py-2 text-center bg-indigo-500 text-white font-medium rounded-lg hover:bg-indigo-600 transition duration-150">
                    Modifier le Profil
                </a>

            </div>
        @else
            <p class="text-center text-red-500">Vous devez être connecté pour voir cette page.</p>
        @endif

    </div>


    {{-- ... (votre en-tête de profil) ... --}}

<div class="flex items-start justify-center p-6 bg-gray-gray-50 min-h-screen">
    <div class="w-full max-w-xl bg-white p-8 rounded-xl shadow-2xl border border-indigo-100">
        
        @if ($user)
            <hr class="w-full border-t border-gray-200 mt-8 mb-6">
            
            <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">
                Meilleurs Scores par Catégorie
            </h2>

            @if ($meilleursScores->isEmpty())
                <p class="text-center text-gray-500">Aucune partie enregistrée pour le moment. Jouez pour établir vos records !</p>
            @else
                <div class="space-y-3">
                    @foreach ($meilleursScores as $scoreRecord)
                        <div class="flex justify-between items-center p-4 rounded-lg bg-indigo-50 border border-indigo-200 shadow-sm">
                            
                            {{-- Catégorie --}}
                            <span class="text-base font-medium text-indigo-800">
                                {{ $scoreRecord->categorie }}
                            </span>
                            
                            {{-- Score --}}
                            <div class="text-right">
                                <span class="text-xl font-extrabold text-gray-900">
                                    {{ number_format($scoreRecord->score, 0, ',', ' ') }} pts
                                </span>
                                {{-- Affichage de la date (Optionnel) --}}
                                <p class="text-xs text-gray-500 mt-1">
                                    Record établi le: {{ \Carbon\Carbon::parse($scoreRecord->date_score)->format('d/m/Y') }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

           
        @else
            <p class="text-center text-red-500">Vous devez être connecté pour voir cette page.</p>
        @endif
    </div>
</div>
</div>