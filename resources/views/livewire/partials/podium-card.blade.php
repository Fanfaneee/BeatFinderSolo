@props(['scoreRecord', 'rank', 'color', 'height'])

<div class="flex flex-col items-center w-1/3 text-center">
    
    {{-- Rang et Nom de la Catégorie --}}
    <div class="text-sm font-bold text-gray-700 mb-1">
        #{{ $rank }}
    </div>

    {{-- Bloc Podium --}}

    <div 
        class="{{ $height }} w-full rounded-t-lg shadow-xl flex flex-col justify-end p-2 border-x border-t border-gray-400"
        style="{{ $color }}" 
    >
        <p class="text-lg font-extrabold text-gray-900 leading-tight">
            {{ number_format($scoreRecord->score, 0, ',', ' ') }}
        </p>
    </div>

    {{-- Étiquette du Score --}}
    <div class="bg-white w-full rounded-b-lg p-1 text-xs shadow-md border-x border-b border-gray-200">
        <p class="font-semibold text-indigo-700 leading-tight truncate">
            {{ $scoreRecord->categorie }}
        </p>
    </div>
</div>