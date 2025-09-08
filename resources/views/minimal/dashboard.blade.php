<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - E-Schoolar</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
        }
        .user-info {
            margin: 20px 0;
            padding: 15px;
            background: #f9f9f9;
            border-left: 4px solid #3490dc;
        }
        .btn {
            display: inline-block;
            padding: 8px 16px;
            background: #3490dc;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .btn:hover {
            background: #2779bd;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Tableau de bord</h1>
        
        @auth
            <div class="user-info">
                <h2>Bienvenue, {{ $user->name }} !</h2>
                <p>Email: {{ $user->email }}</p>
            </div>
            
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn">Déconnexion</button>
            </form>
        @else
            <p>Vous n'êtes pas connecté.</p>
            <a href="{{ route('login') }}" class="btn">Se connecter</a>
        @endauth
    </div>
</body>
</html>
