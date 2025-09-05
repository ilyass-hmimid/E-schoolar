<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Erreur') - {{ config('app.name', 'E-Schoolar') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        .error-container {
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 2rem;
            background-color: #f9fafb;
        }
        .error-code {
            font-size: 6rem;
            font-weight: 700;
            color: #4f46e5;
            margin-bottom: 1rem;
            line-height: 1;
        }
        .error-title {
            font-size: 1.875rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: #111827;
        }
        .error-message {
            font-size: 1.125rem;
            color: #6b7280;
            margin-bottom: 2rem;
            max-width: 42rem;
        }
        .error-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            justify-content: center;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 1.5rem;
            border-radius: 0.375rem;
            font-weight: 500;
            transition: all 0.2s;
            text-decoration: none;
        }
        .btn-primary {
            background-color: #4f46e5;
            color: white;
        }
        .btn-primary:hover {
            background-color: #4338ca;
        }
        .btn-secondary {
            background-color: #e5e7eb;
            color: #374151;
        }
        .btn-secondary:hover {
            background-color: #d1d5db;
        }
    </style>
</head>
<body class="font-sans antialiased">
    <div class="error-container">
        @yield('content')
    </div>
</body>
</html>
