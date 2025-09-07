<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Allo Tawjih</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
<body class="bg-black font-['Poppins'] text-gray-100">
    <div class="min-h-screen flex">
        <!-- Left Side - Hero -->
        <div class="hidden lg:flex flex-col justify-center items-center w-1/2 bg-black p-12 border-r border-gray-800">
            <div class="max-w-md text-center">
                <div class="mb-8">
                    <i class="fas fa-graduation-cap text-6xl bg-gradient-to-r from-[#00C2FF] to-[#00FF94] bg-clip-text text-transparent"></i>
                </div>
                <h1 class="text-5xl font-bold mb-6 bg-gradient-to-r from-[#00C2FF] to-[#00FF94] bg-clip-text text-transparent">Allo Tawjih</h1>
                <p class="text-xl text-gray-300 mb-8">Votre plateforme d'orientation professionnelle</p>
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
        <div class="flex-1 flex items-center justify-center p-8 bg-black">
            <div class="w-full max-w-md p-10 rounded-2xl bg-gray-900/90 backdrop-blur-sm border border-gray-800/50">
                <div class="text-center mb-10">
                    <h2 class="text-3xl font-bold text-white mb-2">Connexion</h2>
                    <p class="text-gray-400">Entrez vos identifiants pour accéder à l'administration</p>
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
                    <div class="mb-6 p-4 bg-red-900/20 border border-red-500/30 rounded-lg">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-circle text-red-400"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-400">Erreur de connexion</h3>
                                <div class="mt-2 text-sm text-red-400">
                                    <ul class="list-disc pl-5 space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf
                    
                    @if(request()->has('redirect'))
                        <input type="hidden" name="redirect" value="{{ request('redirect') }}">
                    @endif

                    <!-- Email -->
                    <div class="space-y-2">
                        <label for="email" class="block text-sm font-medium text-gray-300 mb-1">Adresse email</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-500"></i>
                            </div>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                                class="block w-full pl-10 pr-3 py-3 bg-gray-700/50 border border-gray-600 rounded-lg focus:ring-2 focus:ring-[#00C2FF] focus:border-transparent transition duration-200 text-white placeholder-gray-400"
                                placeholder="votre@email.com">
                        </div>
                        @error('email')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="space-y-2">
                        <label for="password" class="block text-sm font-medium text-gray-300">Mot de passe</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-500"></i>
                            </div>
                            <input id="password" type="password" name="password" required autocomplete="current-password"
                                class="block w-full pl-10 pr-10 py-3 bg-gray-700/50 border border-gray-600 rounded-lg focus:ring-2 focus:ring-[#00C2FF] focus:border-transparent transition duration-200 text-white placeholder-gray-400"
                                placeholder="••••••••">
                            <button type="button" data-target="password" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <i class="far fa-eye text-gray-500 hover:text-gray-300 cursor-pointer"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember_me" type="checkbox" name="remember" class="h-4 w-4 text-[#00C2FF] focus:ring-[#00C2FF] border-gray-600 rounded bg-gray-700/50">
                            <label for="remember_me" class="ml-2 block text-sm text-gray-300">
                                Se souvenir de moi
                            </label>
                        </div>

                        @if (Route::has('password.request'))
                            <a class="text-sm text-[#00C2FF] hover:text-[#00a3d4] font-medium transition-colors" href="{{ route('password.request') }}">
                                Mot de passe oublié ?
                            </a>
                        @endif
                    </div>

                    <button type="submit" class="w-full py-3 px-4 bg-gradient-to-r from-[#00C2FF] to-[#00FF94] text-gray-900 font-medium rounded-lg hover:opacity-90 transition duration-200 shadow-lg hover:shadow-xl hover:shadow-[#00C2FF]/20">
                        Se connecter
                    </button>

                    @if (Route::has('register'))
                        <div class="mt-8 text-center">
                            <p class="text-sm text-gray-400">
                                Vous n'avez pas de compte ?
                                <a href="{{ route('register') }}" class="text-[#00C2FF] hover:text-[#00a3d4] font-medium ml-1 transition-colors">
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
            </div>
        </div>
    </div>
</body>
</html>
