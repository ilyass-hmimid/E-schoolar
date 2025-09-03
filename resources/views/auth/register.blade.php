<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Allo Tawjih</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { 
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif; 
            @apply bg-gray-900 text-white
        }
    </style>
</head>
<body>
    <div class="min-h-screen flex">
        <!-- Left Side - Hero -->
        <div class="hidden lg:flex flex-col justify-center items-center w-1/2 bg-gradient-to-br from-green-600 to-green-800 p-12">
            <div class="max-w-md text-center">
                <h1 class="text-4xl font-bold mb-4">Rejoignez-nous</h1>
                <p class="text-green-100 mb-8">Créez votre compte pour accéder à tous nos services d'orientation scolaire et professionnelle.</p>
                <div class="space-y-4 text-left">
                    <div class="flex items-start">
                        <i class="fas fa-check-circle text-green-300 mt-1 mr-3"></i>
                        <div>
                            <h3 class="font-medium text-green-50">Accompagnement personnalisé</h3>
                            <p class="text-green-100 text-sm">Des conseillers dédiés pour vous guider</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-check-circle text-green-300 mt-1 mr-3"></i>
                        <div>
                            <h3 class="font-medium text-green-50">Ressources exclusives</h3>
                            <p class="text-green-100 text-sm">Accès à des contenus éducatifs de qualité</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Register Form -->
        <div class="flex-1 flex items-center justify-center p-8">
            <div class="w-full max-w-md">
                <div class="flex justify-center mb-8">
                    <img src="{{ asset('images/logo.png') }}" alt="Allo Tawjih" class="h-16" onerror="this.style.display='none'">
                </div>
                
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-gray-100 mb-2">Créer un compte</h2>
                    <p class="text-gray-400">Remplissez le formulaire ci-dessous pour vous inscrire</p>
                </div>
                
                <!-- Contenu -->
                <div class="p-8">
                    <!-- Formulaire -->
                    <form method="POST" action="{{ route('register') }}" class="space-y-6">
                        @csrf
                        
                        <!-- Champ nom complet -->
                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-300 mb-1">
                                Nom complet
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-user text-gray-400"></i>
                                </div>
                                <input 
                                    id="name" 
                                    type="text" 
                                    name="name" 
                                    value="{{ old('name') }}" 
                                    required 
                                    autofocus 
                                    autocomplete="name"
                                    class="bg-gray-800 border border-gray-700 text-white text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full pl-10 p-2.5"
                                    placeholder="Votre nom complet"
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
                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-300 mb-1">
                                Adresse email
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-envelope text-gray-400"></i>
                                </div>
                                <input 
                                    id="email" 
                                    type="email" 
                                    name="email" 
                                    value="{{ old('email') }}" 
                                    required 
                                    autocomplete="email"
                                    class="bg-gray-800 border border-gray-700 text-white text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full pl-10 p-2.5"
                                    placeholder="votre@email.com"
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
                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-300 mb-1">
                                Mot de passe
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-gray-400"></i>
                                </div>
                                <input 
                                    id="password" 
                                    type="password" 
                                    name="password" 
                                    required 
                                    autocomplete="new-password"
                                    class="bg-gray-800 border border-gray-700 text-white text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full pl-10 p-2.5 pr-10"
                                    placeholder="••••••••"
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
                        <!-- Confirm Password -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-300 mb-1">
                                Confirmer le mot de passe
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-gray-400"></i>
                                </div>
                                <input 
                                    id="password_confirmation" 
                                    type="password" 
                                    name="password_confirmation" 
                                    required 
                                    autocomplete="new-password"
                                    class="bg-gray-800 border border-gray-700 text-white text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full pl-10 p-2.5 pr-10"
                                    placeholder="••••••••"
                                    type="password" 
                                    name="password_confirmation" 
                                    required 
                                    autocomplete="new-password" 
                                    class="input-field"
                                >
                            </div>
                        </div>
                        
                        <!-- Conditions d'utilisation -->
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input 
                                    id="terms" 
                                    name="terms" 
                                    type="checkbox" 
                                    class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-600 rounded bg-gray-700"
                                    required
                                >
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="terms" class="text-gray-300">
                                    J'accepte les <a href="#" class="text-green-400 hover:text-green-300">conditions d'utilisation</a> et la <a href="#" class="text-green-400 hover:text-green-300">politique de confidentialité</a>
                                </label>
                            </div>
                        </div>
                        
                        <!-- Bouton d'inscription -->
                        <div>
                            <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                                S'inscrire
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
            
            <!-- Scripts -->
            <script>
                // Toggle password visibility
                document.querySelectorAll('[data-toggle-password]').forEach(button => {
                    button.addEventListener('click', function() {
                        const targetId = this.getAttribute('data-toggle-password');
                        const input = document.getElementById(targetId);
                        const icon = this.querySelector('i');
                        
                        if (input.type === 'password') {
                            input.type = 'text';
                            icon.classList.remove('fa-eye');
                            icon.classList.add('fa-eye-slash');
                        } else {
                            input.type = 'password';
                            icon.classList.remove('fa-eye-slash');
                            icon.classList.add('fa-eye');
                        }
                    });
                });
            </script>
        </div>
    </div>
</body>
</html>
