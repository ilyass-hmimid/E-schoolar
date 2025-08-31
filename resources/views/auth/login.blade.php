<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Allo Tawjih</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #00FF88;
            --dark: #0A0A0A;
            --darker: #050505;
            --dark-gray: #1A1A1A;
            --light: #FFFFFF;
            --transition: all 0.3s ease;
        }
        
        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
        }
        
        body { 
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif; 
            background: var(--dark); 
            color: var(--light); 
            line-height: 1.6;
            overflow-x: hidden;
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
    <div class="login-container">
        <!-- Left Side - Hero -->
        <div class="login-hero">
            <div class="hero-content">
                <h1>Bienvenue sur Allo Tawjih</h1>
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

        <!-- Right Side - Form -->
        <div class="login-form-container">
            <div class="form-wrapper">
                <div class="logo">
                    <img src="{{ asset('images/logo.png') }}" alt="Allo Tawjih" onerror="this.style.display='none'">
                </div>
                
                <div class="form-header">
                    <h2>Connexion</h2>
                    <p>Entrez vos identifiants pour accéder à votre compte</p>
                </div>

                @if (session('status'))
                    <div class="alert alert-success" style="background: rgba(0, 255, 136, 0.1); border: 1px solid rgba(0, 255, 136, 0.2); color: #00e67a; padding: 12px 15px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
                        {{ session('status') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-error" style="background: rgba(255, 71, 71, 0.1); border: 1px solid rgba(255, 71, 71, 0.2); color: #ff6b6b; padding: 12px 15px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-error" style="background: rgba(255, 71, 71, 0.1); border: 1px solid rgba(255, 71, 71, 0.2); color: #ff6b6b; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
                        <h4 style="margin-bottom: 10px; font-size: 16px; font-weight: 600;">Erreur de connexion</h4>
                        <ul style="list-style: none; padding-left: 0; margin: 0;">
                            @foreach ($errors->all() as $error)
                                <li style="margin-bottom: 5px; padding-left: 20px; position: relative;">
                                    <i class="fas fa-exclamation-circle" style="position: absolute; left: 0; top: 4px;"></i>
                                    {{ $error }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="form-group">
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                               name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                               placeholder="Adresse email">
                    </div>

                    <div class="form-group">
                        <input id="password" type="password" 
                               class="form-control @error('password') is-invalid @enderror" 
                               name="password" required autocomplete="current-password"
                               placeholder="Mot de passe">
                    </div>

                    <div class="remember-forgot">
                        <label class="remember-me">
                            <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <span>Se souvenir de moi</span>
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="forgot-password">
                                Mot de passe oublié ?
                            </a>
                        @endif
                    </div>

                    <button type="submit" class="btn">
                        Se connecter
                    </button>
                </form>

                @if (Route::has('register'))
                    <div class="form-footer">
                        <p style="color: #aaa; font-size: 0.9rem; margin-top: 20px;">
                            Vous n'avez pas de compte ?
                            <a href="{{ route('register') }}" style="color: var(--primary); text-decoration: none; font-weight: 600; margin-left: 5px;">
                                S'inscrire
                            </a>
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
