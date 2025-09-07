<x-guest-layout>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-blue-50 to-gray-50">
        <div class="w-full sm:max-w-md px-6 py-8 bg-white shadow-lg overflow-hidden sm:rounded-xl">
            <!-- Logo -->
            <div class="flex justify-center mb-8">
                <a href="{{ getDashboardUrl() }}" class="flex items-center space-x-2">
                    <x-application-logo class="h-12 w-auto text-primary-600" />
                    <span class="text-2xl font-bold text-gray-900">{{ config('app.name', 'Allo Tawjih') }}</span>
                </a>
            </div>

            <!-- Icône de vérification -->
            <div class="flex justify-center mb-6">
                <div class="bg-blue-100 p-4 rounded-full">
                    <i class="fas fa-envelope-open-text text-4xl text-blue-600"></i>
                </div>
            </div>

            <!-- Titre -->
            <h2 class="text-center text-2xl font-bold text-gray-900 mb-4">
                Vérifiez votre adresse email
            </h2>

            <!-- Message -->
            <div class="text-center text-gray-600 mb-8">
                <p class="mb-4">
                    Merci pour votre inscription ! Avant de commencer, veuillez vérifier votre adresse email en cliquant sur le lien que nous venons de vous envoyer.
                </p>
                <p>
                    Si vous n'avez pas reçu l'email, nous vous en enverrons un autre avec plaisir.
                </p>
            </div>

            <!-- Message de succès -->
            @if (session('status') == 'verification-link-sent')
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 mr-2"></i>
                        <p class="text-sm font-medium text-green-800">
                            Un nouveau lien de vérification a été envoyé à l'adresse email que vous avez fournie lors de l'inscription.
                        </p>
                    </div>
                </div>
            @endif

            <!-- Boutons d'action -->
            <div class="space-y-4">
                <!-- Bouton de renvoi d'email -->
                <form method="POST" action="{{ route('verification.send') }}" class="w-full">
                    @csrf
                    <x-primary-button class="w-full justify-center py-2.5 text-base font-medium rounded-lg">
                        {{ __('Renvoyer l\'email de vérification') }}
                        <i class="fas fa-redo ml-2"></i>
                    </x-primary-button>
                </form>

                <!-- Bouton de déconnexion -->
                <form method="POST" action="{{ route('admin.logout') }}" class="w-full">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center px-4 py-2.5 border border-transparent text-sm font-medium rounded-lg text-primary-600 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        {{ __('Se déconnecter') }}
                    </button>
                </form>
            </div>

            <!-- Ligne de séparation -->
            <div class="mt-8 relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-200"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-white text-gray-500">
                        Vous rencontrez un problème ?
                    </span>
                </div>
            </div>

            <!-- Lien d'aide -->
            <div class="mt-4 text-center">
                <a href="{{ route('contact') }}" class="text-sm font-medium text-primary-600 hover:text-primary-500">
                    Contactez le support
                </a>
            </div>
        </div>
    </div>
</x-guest-layout>
