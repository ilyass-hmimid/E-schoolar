<x-guest-layout>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-blue-50 to-gray-50">
        <div class="w-full sm:max-w-md px-6 py-8 bg-white shadow-lg overflow-hidden sm:rounded-xl">
            <!-- Logo -->
            <div class="flex justify-center mb-8">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
                    <x-application-logo class="h-12 w-auto text-primary-600" />
                    <span class="text-2xl font-bold text-gray-900">{{ config('app.name', 'Allo Tawjih') }}</span>
                </a>
            </div>

            <!-- Titre -->
            <h2 class="text-center text-2xl font-bold text-gray-900 mb-8">
                Créer un compte
            </h2>

            <!-- Formulaire -->
            <form method="POST" action="{{ route('register') }}" class="space-y-6">
                @csrf

                <!-- Nom complet -->
                <div>
                    <x-input-label for="name" :value="__('Nom complet')" class="block text-sm font-medium text-gray-700" />
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-user text-gray-400"></i>
                        </div>
                        <x-text-input 
                            id="name" 
                            type="text" 
                            name="name" 
                            :value="old('name')" 
                            required 
                            autofocus 
                            autocomplete="name" 
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500"
                            placeholder="Votre nom complet"
                        />
                    </div>
                    <x-input-error :messages="$errors->get('name')" class="mt-1 text-sm text-red-600" />
                </div>

                <!-- Email -->
                <div>
                    <x-input-label for="email" :value="__('Adresse email')" class="block text-sm font-medium text-gray-700" />
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <x-text-input 
                            id="email" 
                            type="email" 
                            name="email" 
                            :value="old('email')" 
                            required 
                            autocomplete="email" 
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500"
                            placeholder="votre@email.com"
                        />
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-1 text-sm text-red-600" />
                </div>

                <!-- Mot de passe -->
                <div>
                    <x-input-label for="password" :value="__('Mot de passe')" class="block text-sm font-medium text-gray-700" />
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <x-text-input 
                            id="password" 
                            type="password" 
                            name="password" 
                            required 
                            autocomplete="new-password" 
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500"
                            placeholder="••••••••"
                        />
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-1 text-sm text-red-600" />
                </div>

                <!-- Confirmation du mot de passe -->
                <div>
                    <x-input-label for="password_confirmation" :value="__('Confirmer le mot de passe')" class="block text-sm font-medium text-gray-700" />
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <x-text-input 
                            id="password_confirmation" 
                            type="password" 
                            name="password_confirmation" 
                            required 
                            autocomplete="new-password" 
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500"
                            placeholder="••••••••"
                        />
                    </div>
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-sm text-red-600" />
                </div>

                <!-- Conditions d'utilisation -->
                <div class="flex items-start">
                    <div class="flex items-center h-5">
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
                            J'accepte les <a href="#" class="text-primary-600 hover:text-primary-500">conditions d'utilisation</a> et la <a href="#" class="text-primary-600 hover:text-primary-500">politique de confidentialité</a>
                        </label>
                    </div>
                </div>

                <!-- Bouton d'inscription -->
                <div>
                    <x-primary-button class="w-full justify-center py-2.5 text-base font-medium rounded-lg">
                        {{ __('Créer mon compte') }}
                        <i class="fas fa-user-plus ml-2"></i>
                    </x-primary-button>
                </div>
            </form>

            <!-- Ligne de séparation -->
            <div class="mt-8 relative">
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
            <div class="mt-4 text-center">
                <a href="{{ route('login') }}" class="font-medium text-primary-600 hover:text-primary-500">
                    Se connecter
                </a>
            </div>
        </div>
    </div>
</x-guest-layout>
