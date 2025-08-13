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
            <h2 class="text-center text-2xl font-bold text-gray-900 mb-2">
                Réinitialisation du mot de passe
            </h2>
            <p class="text-center text-gray-600 mb-8">
                Entrez votre nouveau mot de passe pour accéder à votre compte.
            </p>

            <!-- Formulaire -->
            <form method="POST" action="{{ route('password.store') }}" class="space-y-6">
                @csrf

                <!-- Password Reset Token -->
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <!-- Email (caché) -->
                <input type="hidden" name="email" value="{{ old('email', $request->email) }}">
                
                <!-- Nouveau mot de passe -->
                <div>
                    <x-input-label for="password" :value="__('Nouveau mot de passe')" class="block text-sm font-medium text-gray-700" />
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
                    <p class="mt-1 text-xs text-gray-500">
                        Utilisez au moins 8 caractères avec des chiffres et des lettres.
                    </p>
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

                <!-- Bouton de réinitialisation -->
                <div>
                    <x-primary-button class="w-full justify-center py-2.5 text-base font-medium rounded-lg">
                        {{ __('Réinitialiser le mot de passe') }}
                        <i class="fas fa-sync-alt ml-2"></i>
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
                        Vous vous souvenez de votre mot de passe ?
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
