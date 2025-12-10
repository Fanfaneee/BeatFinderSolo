<x-layouts.app>
    {{-- Le contenu du formulaire sera injecté dans le $slot de votre layout --}}

    <div class="flex min-h-screen items-center justify-center bg-gray-100">
        <div class="w-full max-w-md p-8 space-y-6 bg-white rounded-lg shadow-xl">
            
            <h2 class="text-2xl font-bold text-center text-gray-900">
                Créer un Compte
            </h2>
            
            @if ($errors->any())
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50" role="alert">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <form class="space-y-6" method="POST" action="{{ route('register') }}">
                @csrf

                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700">Nom d'utilisateur</label>
                    <div class="mt-1">
                        <input 
                            id="username" 
                            name="username" 
                            type="text" 
                            required 
                            autofocus 
                            value="{{ old('username') }}"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm sm:text-sm"
                            placeholder="Choisissez un nom d'utilisateur"
                        >
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Mot de passe</label>
                    <div class="mt-1">
                        <input 
                            id="password" 
                            name="password" 
                            type="password" 
                            required 
                            autocomplete="new-password"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm sm:text-sm"
                        >
                    </div>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmer le Mot de passe</label>
                    <div class="mt-1">
                        <input 
                            id="password_confirmation" 
                            name="password_confirmation" 
                            type="password" 
                            required 
                            autocomplete="new-password"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm sm:text-sm"
                        >
                    </div>
                </div>

                <div>
                    <button 
                        type="submit" 
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        S'inscrire
                    </button>
                </div>
            </form>

            <div class="text-sm text-center">
                <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                    Déjà un compte ? Connectez-vous
                </a>
            </div>
        </div>
    </div>
</x-layouts.app>