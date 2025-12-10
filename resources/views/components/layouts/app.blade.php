<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
   
    <header class="bg-white shadow p-4"> {{-- Ajout de p-4 pour l'espace et un peu de style --}}
    
    <nav class="flex justify-between items-center max-w-7xl mx-auto space-x-4">
       
          <a href="/" class="text-xl font-bold text-gray-900">BeatFinderSolo</a>
        
        <div class="space-x-4">
            {{-- Lien Accueil (toujours visible) --}}
            <a href="/" class="text-gray-600 hover:text-gray-900">Accueil</a>

            @auth 
               
               
                
                <a href="/admin/musiques/ajouter" class="text-gray-600 hover:text-gray-900">Admin</a>
                
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="text-gray-600 hover:text-gray-900">Se déconnecter</button>
                </form>
            @else
                @if (!Route::is('login') && !Route::is('register'))
                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900">Se connecter</a>
                    <a href="{{ route('register') }}" class="text-indigo-600 font-medium hover:text-indigo-700">S'inscrire</a>
                @endif
            @endauth
        </div>
    </nav>
</header>
   
    {{ $slot }} 
    @livewireScripts
</body>
</html>