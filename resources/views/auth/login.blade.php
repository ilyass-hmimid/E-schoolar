<x-guest-layout>
    <div class="min-h-screen flex flex-col justify-center items-center p-4">
        <div class="w-full max-w-md fade-in">
            <!-- Carte principale -->
            <div class="bg-white rounded-xl shadow-xl overflow-hidden">
                <!-- En-tête avec dégradé -->
                <div class="bg-gradient-to-r from-primary-600 to-primary-800 p-6 text-center">
                    <div class="flex justify-center mb-4">
                        <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
                            <x-application-logo class="h-10 w-auto text-white" />
                            <span class="text-2xl font-bold text-white">Bienvenue Sur<br/>Allo Tawjih</span>
                        </a>
                    </div>
                    <p class="text-primary-100 text-sm">Accédez à votre espace personnel</p>
                </div>
                
                <!-- Contenu -->
                <div class="p-8">
                    <!-- Message de statut -->
                    @if (session('status'))
                        <div class="mb-6 p-4 bg-green-50 text-green-700 rounded-lg text-sm">
                            {{ session('status') }}
                        </div>
                    @endif
                    
                    <!-- Formulaire -->
                    <form method="POST" action="{{ route('login') }}" class="space-y-6">
                        @csrf
                        
                        <!-- Champ email -->
                        <div class="space-y-2">
                            <label for="email" class="block text-sm font-medium text-gray-700">
                                Adresse email
                            </label>
                            <div class="input-group">
                                <input 
                                    id="email" 
                                    type="email" 
                                    name="email" 
                                    value="{{ old('email') }}"
                                    required 
                                    autofocus 
                                    autocomplete="email" 
                                    class="input-field"
                                    placeholder="votre@email.com"
                                >
                            </div>
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Champ mot de passe -->
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <label for="password" class="block text-sm font-medium text-gray-700">
                                    Mot de passe
                                </label>
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="text-sm font-medium text-primary-600 hover:text-primary-500 transition-colors">
                                        Mot de passe oublié ?
                                    </a>
                                @endif
                            </div>
                            <div class="input-group">
                                <input 
                                    id="password" 
                                    type="password" 
                                    name="password" 
                                    required 
                                    autocomplete="current-password" 
                                    class="input-field"
                                    placeholder="••••••••"
                                >
                            </div>
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                
                        <!-- Bouton de connexion -->
                        <div>
                            <button type="submit" class="w-full btn-primary">
                                <span>Se connecter</span>
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </form>
                    
                    <!-- Séparateur -->
                    <div class="relative my-6">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-200"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white text-gray-500">
                                Nouveau sur Allo Tawjih ?
                            </span>
                        </div>
                    </div>
                    
                    <!-- Lien d'inscription -->
                    <div>
                        <a href="{{ route('register') }}" class="w-full flex items-center justify-center px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                            Créer un compte
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Pied de page -->
            <div class="mt-6 text-center">
                <p class="text-xs text-gray-500">
                    &copy; {{ date('Y') }} {{ config('app.name') }}. Tous droits réservés.
                </p>
            </div>
        </div>
    </div>
    
    @if(session('logout'))
        <script>
            if (window.history.replaceState) {
                window.history.replaceState(null, null, window.location.href);
            }
        </script>
    @endif
</x-guest-layout>
