<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Allo Tawjih') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="/favicon.ico">
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
        
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        
        <!-- Bootstrap Icons -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
        
        <!-- Scripts -->
        @routes
        @vite([
            'resources/sass/app.scss',
            'resources/js/app.js',
            'resources/js/bootstrap.js'
        ])
        
        <style>
            /* Animations */
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(10px); }
                to { opacity: 1; transform: translateY(0); }
            }
            
            .fade-in {
                animation: fadeIn 0.5s ease-out forwards;
            }
            
            /* Styles globaux */
            body {
                font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
                background: linear-gradient(135deg, #f0f9ff 0%, #f8fafc 100%);
                color: #1e293b;
                min-height: 100vh;
                display: flex;
                flex-direction: column;
            }
            
            h1, h2, h3, h4, h5, h6 {
                font-family: 'Poppins', sans-serif;
                font-weight: 600;
                color: #0f172a;
            }
            
            /* Styles des boutons */
            .btn-primary {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 0.5rem;
                background-color: #0d6efd;
                color: white;
                font-weight: 500;
                padding: 0.625rem 1.5rem;
                border-radius: 0.5rem;
                border: 1px solid transparent;
                transition: all 0.2s ease-in-out;
                text-decoration: none;
            }
            
            .btn-primary:hover {
                background-color: #0b5ed7;
                transform: translateY(-1px);
            }
            
            /* Styles des champs de formulaire */
            .input-field {
                display: block;
                width: 100%;
                padding: 0.625rem 2.5rem 0.625rem 2.5rem;
                font-size: 1rem;
                font-weight: 400;
                line-height: 1.5;
                color: #212529;
                background-color: #fff;
                background-clip: padding-box;
                border: 1px solid #ced4da;
                border-radius: 0.5rem;
                transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
            }
            
            .input-field:focus {
                border-color: #86b7fe;
                outline: 0;
                box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
            }
            
            .input-group {
                position: relative;
                border-radius: 0.375rem;
                box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
                margin-bottom: 1rem;
            }
            
            .input-icon {
                position: absolute;
                top: 50%;
                left: 1rem;
                transform: translateY(-50%);
                color: #6c757d;
                pointer-events: none;
                z-index: 10;
            }
            
            /* Layout */
            #app {
                display: flex;
                flex-direction: column;
                min-height: 100vh;
                width: 100%;
            }
            
            .auth-container {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                flex: 1;
                padding: 2rem 1rem;
            }
            
            .auth-card {
                width: 100%;
                max-width: 28rem;
                background: white;
                border-radius: 1rem;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
                padding: 2.5rem;
                margin: 1rem 0;
            }
            
            .auth-logo {
                text-align: center;
                margin-bottom: 2rem;
            }
            
            .auth-logo img {
                height: 3.5rem;
                width: auto;
            }
            
            .auth-title {
                text-align: center;
                margin-bottom: 2rem;
                color: #1e293b;
            }
            
            .auth-footer {
                text-align: center;
                margin-top: 1.5rem;
                color: #64748b;
                font-size: 0.875rem;
            }
            
            .auth-footer a {
                color: #0d6efd;
                text-decoration: none;
                font-weight: 500;
            }
            
            .auth-footer a:hover {
                text-decoration: underline;
            }
        </style>
    </head>
    <body>
        <div id="app" class="fade-in">
            <div class="auth-container">
                <div class="auth-card">
                    <div class="auth-logo">
                        <a href="{{ route('dashboard') }}">
                            <x-application-logo style="height: 3.5rem; width: auto;" />
                        </a>
                    </div>
                    
                    <h1 class="auth-title">
                        @yield('title', 'Bienvenue')
                    </h1>
                    
                    {{ $slot }}
                    
                    @hasSection('auth-footer')
                        <div class="auth-footer">
                            @yield('auth-footer')
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <script>
            // Ajout de la classe d'animation après le chargement de la page
            document.addEventListener('DOMContentLoaded', function() {
                const elements = document.querySelectorAll('.fade-in');
                elements.forEach((el, index) => {
                    el.style.animationDelay = `${index * 0.1}s`;
                });
            });
        </script>
        
        @stack('scripts')
    </body>
</html>
