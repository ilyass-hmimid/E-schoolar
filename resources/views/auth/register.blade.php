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
                            <span class="text-2xl font-bold text-white">{{ config('app.name') }}</span>
                        </a>
                    </div>
                    <p class="text-primary-100 text-sm">Rejoignez notre communauté éducative</p>
                </div>
                
                <!-- Contenu -->
                <div class="p-8">
                    <!-- Formulaire -->
                    <form method="POST" action="{{ route('register') }}" class="space-y-6">
                        @csrf
                        
                        <!-- Champ nom complet -->
                        <div class="space-y-2">
                            <label for="name" class="block text-sm font-medium text-gray-700">
                                Nom complet
                            </label>
                            <div class="input-group">
                                <div class="input-icon">
                                    <i class="fas fa-user"></i>
                                </div>
                                <input 
                                    id="name" 
                                    type="text" 
                                    name="name" 
                                    value="{{ old('name') }}"
                                    required 
                                    autofocus 
                                    autocomplete="name" 
                                    class="input-field"
                                    placeholder="Votre nom complet"
                                >
                            </div>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Champ email -->
                        <div class="space-y-2">
                            <label for="email" class="block text-sm font-medium text-gray-700">
                                Adresse email
                            </label>
                            <div class="input-group">
                                <div class="input-icon">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <input 
                                    id="email" 
                                    type="email" 
                                    name="email" 
                                    value="{{ old('email') }}"
                                    required 
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
                            <label for="password" class="block text-sm font-medium text-gray-700">
                                Mot de passe
                            </label>
                            <div class="input-group">
                                <div class="input-icon">
                                    <i class="fas fa-lock"></i>
                                </div>
                                <input 
                                    id="password" 
                                    type="password" 
                                    name="password" 
                                    required 
                                    autocomplete="new-password" 
                                    class="input-field"
                                    placeholder="••••••••"
                                >
                            </div>
                            <p class="mt-1 text-xs text-gray-500">
                                Utilisez au moins 8 caractères avec des lettres, des chiffres et des symboles.
                            </p>
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Confirmation du mot de passe -->
                        <div class="space-y-2">
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                                Confirmer le mot de passe
                            </label>
                            <div class="input-group">
                                <div class="input-icon">
                                    <i class="fas fa-lock"></i>
                                </div>
                                <input 
                                    id="password_confirmation" 
                                    type="password" 
                                    name="password_confirmation" 
                                    required 
                                    autocomplete="new-password" 
                                    class="input-field"
                                    placeholder="••••••••"
                                >
                            </div>
                        </div>
                        
                        <!-- Conditions d'utilisation -->
                        <div class="flex items-start">
                            <div class="flex items-center h-5 mt-1">
                                <input 
                                    id="terms" 
                                    name="terms" 
                                    type="checkbox" 
                                    class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
                                    required
                                >
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="terms" class="font-medium text-gray-700">
                                    J'accepte les <a href="#" class="text-primary-600 hover:text-primary-500 transition-colors">conditions d'utilisation</a> et la <a href="#" class="text-primary-600 hover:text-primary-500 transition-colors">politique de confidentialité</a>.
                                </label>
                            </div>
                        </div>
                        
                        <!-- Bouton d'inscription -->
                        <div>
                            <button type="submit" class="w-full btn-primary">
                                <span>Créer mon compte</span>
                                <i class="fas fa-user-plus"></i>
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
                                Vous avez déjà un compte ?
                            </span>
                        </div>
                    </div>
                    
                    <!-- Lien de connexion -->
                    <div>
                        <a href="{{ route('login') }}" class="w-full flex items-center justify-center px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                            Se connecter à mon compte
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
</x-guest-layout>
