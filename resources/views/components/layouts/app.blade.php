<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cherry+Bomb+One&display=swap" rel="stylesheet">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Titan+One&display=swap" rel="stylesheet">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Luckiest+Guy&display=swap" rel="stylesheet">

</head>
<body class="bg-wtt-noise-gradient">
   
    <header class=" "> 
    
    <nav class="flex justify-between items-center mx-10  space-x-4">
       
          <a href="/" class="text-xl font-bold text-gray-900"><img src="/storage/images/logo_beat_finder_solo.png" alt="Logo" class="h-30"></a>

        
        <div class="space-x-4 text-xl">
            {{-- Lien Accueil (toujours visible) --}}
            <a href="/" class="text-bleu font border-text hover:text-gray-900">Accueil</a>

            
               
               
                @auth 
                @admin
                <a href="/admin/musiques/ajouter" class="text-bleu font hover:text-gray-900">Admin</a>
                @endadmin
                <a href="/profil" class="text-bleu font hover:text-gray-900">Profil</a>
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="text-bleu font cursor-pointer hover:text-white">Se déconnecter</button>
                </form>
            @else
                @if (!Route::is('login') && !Route::is('register'))
                    <a href="{{ route('login') }}" class="text-bleu font hover:text-gray-900">Se connecter</a>
                    <a href="{{ route('register') }}" class="text-bleu font font-medium hover:0text-indigo-70">S'inscrire</a>
                @endif
            @endauth
        </div>
    </nav>
</header>
   
    {{ $slot }} 
    @livewireScripts
</body>
</html>