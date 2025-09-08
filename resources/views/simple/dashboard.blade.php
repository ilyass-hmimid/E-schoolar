<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - E-Schoolar</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
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
            margin-top: 0;
        }
        .user-info {
            background: #f9f9f9;
            padding: 15px;
            border-left: 4px solid #3490dc;
            margin-bottom: 20px;
        }
        .btn {
            display: inline-block;
            background: #3490dc;
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 4px;
            border: none;
            cursor: pointer;
        }
        .btn:hover {
            background: #2779bd;
        }
        .logout-form {
            display: inline;
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
                <p>Connecté en tant qu'administrateur</p>
            </div>
            
            <form method="POST" action="{{ route('logout') }}" class="logout-form">
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
