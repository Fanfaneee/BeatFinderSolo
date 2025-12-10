<div>
@auth
<div class="py-12">
        <livewire:lobby />
    </div>
@else
    <div class="max-w-2xl mx-auto mt-12 p-6 bg-white rounded-lg shadow-xl">
        <h1 class="text-3xl font-bold mb-4 text-indigo-700">Bienvenue sur BeatFinderSolo ! 🎵</h1>
        <p class="mb-6 text-gray-700">
            Pour commencer à jouer et découvrir des musiques passionnantes, veuillez vous connecter ou créer un compte.
        </p>
        <div class="space-x-4">
            <a href="{{ route('login') }}" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Se connecter</a>
            <a href="{{ route('register') }}" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Créer un compte</a>
        </div>
    </div>
@endauth


</div>
