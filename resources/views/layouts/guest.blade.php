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
        
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
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
                font-family: 'Inter', sans-serif;
                @apply bg-gradient-to-br from-blue-50 to-gray-50 text-gray-800;
            }
            
            h1, h2, h3, h4, h5, h6 {
                font-family: 'Poppins', sans-serif;
                @apply font-semibold;
            }
            
            /* Styles des boutons et champs de formulaire */
            .btn-primary {
                @apply bg-primary-600 hover:bg-primary-700 text-white font-medium py-2.5 px-6 rounded-lg transition-all duration-200 flex items-center justify-center space-x-2;
            }
            
            .input-field {
                @apply block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200;
            }
            
            .input-group {
                @apply relative rounded-md shadow-sm;
            }
            
            .input-icon {
                @apply absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400;
            }
        </style>
    </head>
    <body class="antialiased">
        {{ $slot }}
        
        <script>
            // Ajout de la classe d'animation après le chargement de la page
            document.addEventListener('DOMContentLoaded', function() {
                const elements = document.querySelectorAll('.fade-in');
                elements.forEach((el, index) => {
                    el.style.animationDelay = `${index * 0.1}s`;
                });
            });
        </script>
    </body>
</html>
