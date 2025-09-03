<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Allo Tawjih</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --primary: #00FF88;
            --dark: #0A0A0A;
            --darker: #050505;
            --dark-gray: #1A1A1A;
            --light: #FFFFFF;
            --transition: all 0.3s ease;
        }
        
        body { 
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif; 
            @apply bg-gray-900 text-white
        }
        
        .container { 
            max-width: 1400px; 
            margin: 0 auto; 
            padding: 0; 
            width: 100%;
        }
        
        .btn {
            background: var(--primary); 
            color: var(--darker); 
            padding: 12px 30px;
            border-radius: 50px; 
            text-decoration: none; 
            font-weight: 600; 
            transition: var(--transition);
            border: none;
            cursor: pointer;
            display: inline-block;
            font-size: 1rem;
            text-align: center;
            width: 100%;
        }
        
        .btn:hover { 
            transform: translateY(-3px); 
            box-shadow: 0 10px 20px rgba(0, 255, 136, 0.2);
        }

        /* Layout */
        .login-container {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }

        /* Left Side - Hero */
        .login-hero {
            flex: 1;
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), 
                        url('https://images.unsplash.com/photo-1523050854058-8df90110c9f1?ixlib=rb-4.0.3&auto=format&fit=crop&w=1470&q=80');
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            position: relative;
            overflow: hidden;
        }

        .login-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, rgba(0, 255, 136, 0.1), transparent);
            z-index: 1;
        }

        .hero-content {
            max-width: 500px;
            position: relative;
            z-index: 2;
            animation: fadeInUp 1s ease;
        }

        .hero-content h1 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 20px;
            line-height: 1.2;
            background: linear-gradient(90deg, #00FF88, #00CCFF);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-content p {
            font-size: 1.1rem;
            color: #ddd;
            margin-bottom: 30px;
        }

        /* Right Side - Form */
        .login-form-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px;
            background: var(--darker);
            position: relative;
        }

        .form-wrapper {
            max-width: 400px;
            width: 100%;
            animation: fadeIn 0.8s ease;
        }

        .logo {
            text-align: center;
            margin-bottom: 40px;
        }

        .logo img {
            height: 60px;
            margin-bottom: 15px;
        }

        .form-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .form-header h2 {
            font-size: 2rem;
            margin-bottom: 10px;
            color: var(--light);
            position: relative;
            display: inline-block;
        }

        .form-header h2::after {
            content: '';
            position: absolute;
            left: 50%;
            bottom: -10px;
            transform: translateX(-50%);
            width: 50px;
            height: 3px;
            background: var(--primary);
            border-radius: 3px;
        }

        .form-header p {
            color: #bbb;
            font-size: 0.95rem;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-control {
            width: 100%;
            padding: 15px 20px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            color: var(--light);
            font-size: 1rem;
            transition: var(--transition);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0, 255, 136, 0.1);
        }

        .form-footer {
            margin-top: 30px;
            text-align: center;
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 20px 0;
            font-size: 0.9rem;
        }

        .remember-me {
            display: flex;
            align-items: center;
        }

        .remember-me input {
            margin-right: 8px;
            accent-color: var(--primary);
        }

        .forgot-password {
            color: var(--primary);
            text-decoration: none;
            transition: var(--transition);
        }

        .forgot-password:hover {
            text-decoration: underline;
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes fadeInUp {
            from { 
                opacity: 0;
                transform: translateY(20px);
            }
            to { 
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive */
        @media (max-width: 992px) {
            .login-container {
                flex-direction: column;
            }

            .login-hero {
                padding: 60px 20px;
                text-align: center;
            }

            .login-form-container {
                padding: 40px 20px;
            }

            .hero-content h1 {
                font-size: 2.5rem;
            }
        }

        @media (max-width: 576px) {
            .hero-content h1 {
                font-size: 2rem;
            }

            .form-header h2 {
                font-size: 1.8rem;
            }

            .login-form-container {
                padding: 30px 15px;
            }
        }
    </style>
</head>
<body>
    <div class="min-h-screen flex">
        <!-- Left Side - Hero -->
        <div class="hidden lg:flex flex-col justify-center items-center w-1/2 bg-gradient-to-br from-green-600 to-green-800 p-12">
            <div class="max-w-md text-center">
                <h1 class="text-4xl font-bold mb-4">Bienvenue sur Allo Tawjih</h1>
                <p>Connectez-vous pour accéder à votre espace personnel et profiter de tous nos services d'orientation scolaire et professionnelle.</p>
                <div class="features">
                    <div class="feature">
                        <i class="fas fa-check-circle"></i>
                        <span>Accompagnement personnalisé</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-check-circle"></i>
                        <span>Ressources exclusives</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-check-circle"></i>
                        <span>Suivi de progression</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="flex-1 flex items-center justify-center p-8">
            <div class="w-full max-w-md">
                <div class="flex justify-center mb-8">
                    <img src="{{ asset('images/logo.png') }}" alt="Allo Tawjih" class="h-16" onerror="this.style.display='none'">
                </div>
                
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-gray-100 mb-2">Connexion</h2>
                    <p class="text-gray-400">Entrez vos identifiants pour accéder à votre compte</p>
                </div>

                @if (session('status'))
                    <div class="bg-green-900/20 border border-green-500/30 text-green-400 px-4 py-3 rounded-lg mb-6 text-sm">
                        {{ session('status') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="bg-red-900/20 border border-red-500/30 text-red-400 px-4 py-3 rounded-lg mb-6 text-sm">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="bg-red-900/20 border border-red-500/30 text-red-400 px-4 py-3 rounded-lg mb-6 text-sm">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li class="flex items-start">
                                    <i class="fas fa-exclamation-circle mt-1 mr-2 flex-shrink-0"></i>
                                    <span>{{ $error }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-300 mb-1">Adresse email</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-400"></i>
                            </div>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" 
                                   class="bg-gray-800 border border-gray-700 text-white text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full pl-10 p-2.5" 
                                   placeholder="votre@email.com">
                        </div>
                        @error('email')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <label for="password" class="block text-sm font-medium text-gray-300">Mot de passe</label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-sm text-green-400 hover:text-green-300 transition-colors">
                                    Mot de passe oublié ?
                                </a>
                            @endif
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                            <input id="password" type="password" name="password" required autocomplete="current-password" 
                                   class="bg-gray-800 border border-gray-700 text-white text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full pl-10 p-2.5 pr-10" 
                                   placeholder="••••••••">
                            <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-300 focus:outline-none" data-target="password">
                                <i class="far fa-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <div class="flex items-center">
                            <input id="remember_me" name="remember" type="checkbox" 
                                   class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-600 rounded bg-gray-700">
                            <label for="remember_me" class="ml-2 block text-sm text-gray-300">
                                Se souvenir de moi
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                        Se connecter
                    </button>

                    @if (Route::has('register'))
                        <div class="text-center text-sm text-gray-400 mt-6">
                            <p>Vous n'avez pas de compte ? 
                                <a href="{{ route('register') }}" class="text-green-400 hover:text-green-300 font-medium transition-colors">
                                    Créer un compte
                                </a>
                            </p>
                        </div>
                    @endif
                </form>

                <script>
                    // Toggle password visibility
                    document.querySelectorAll('[data-target="password"]').forEach(button => {
                        button.addEventListener('click', function() {
                            const input = document.getElementById('password');
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
                @endif
            </div>
        </div>
    </div>
</body>
</html>
