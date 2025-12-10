<x-layouts.app>
<div class="flex items-center justify-center min-h-screen bg-gray-100 p-6">
    
    <div class="w-full max-w-md bg-white p-8 rounded-xl shadow-2xl border border-gray-200">
        
        <h2 class="text-3xl font-extrabold text-center text-indigo-700 mb-6">
            Connexion au Blind Test
        </h2>

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf

            {{-- NOM D'UTILISATEUR --}}
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700 mb-1">
                    Nom d'utilisateur
                </label>
                <input 
                    id="username" 
                    type="text" 
                    name="username" 
                    required 
                    autofocus 
                    placeholder="Votre nom d'utilisateur"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('username') border-red-500 @enderror"
                    value="{{ old('username') }}"
                >
                @error('username')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- MOT DE PASSE --}}
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                    Mot de passe
                </label>
                <input 
                    id="password" 
                    type="password" 
                    name="password" 
                    required 
                    autocomplete="current-password"
                    placeholder="&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('password') border-red-500 @enderror"
                >
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            {{-- BOUTON DE CONNEXION --}}
            <div>
                <button 
                    type="submit"
                    class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-md text-base font-semibold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out"
                >
                    Se connecter
                </button>
            </div>
            
           
        </form>
    </div>
</div>
</x-layouts.app>