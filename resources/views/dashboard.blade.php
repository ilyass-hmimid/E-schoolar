<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - E-Schoolar</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <!-- Barre de navigation -->
    <nav class="bg-white border-b border-gray-200 fixed w-full z-30">
        <div class="px-3 py-3 lg:px-5 lg:pl-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center justify-start">
                    <button id="toggleSidebar" class="lg:hidden text-gray-500 hover:text-gray-600">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                    <a href="#" class="text-xl font-bold flex items-center lg:ml-2.5">
                        <span class="self-center whitespace-nowrap">E-Schoolar</span>
                    </a>
                </div>
                <div class="flex items-center">
                    <!-- Menu utilisateur -->
                    <div class="relative ml-3">
                        <div>
                            <button type="button" class="flex text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600" id="user-menu-button" aria-expanded="false" data-dropdown-toggle="dropdown">
                                <span class="sr-only">Ouvrir le menu utilisateur</span>
                                <div class="h-8 w-8 rounded-full bg-blue-500 text-white flex items-center justify-center">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            </button>
                        </div>
                        <!-- Menu déroulant utilisateur -->
                        <div class="hidden z-50 my-4 text-base list-none bg-white rounded divide-y divide-gray-100 shadow dark:bg-gray-700 dark:divide-gray-600" id="dropdown">
                            <div class="py-3 px-4">
                                <span class="block text-sm text-gray-900 dark:text-white">{{ $user->name }}</span>
                                <span class="block text-sm font-medium text-gray-500 truncate dark:text-gray-400">{{ $user->email }}</span>
                            </div>
                            <ul class="py-1" aria-labelledby="dropdown">
                                <li>
                                    <a href="#" class="block py-2 px-4 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white">Profil</a>
                                </li>
                                <li>
                                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="block py-2 px-4 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white">
                                        Déconnexion
                                    </a>
                                    <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" class="hidden">
                                        @csrf
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Conteneur principal avec sidebar et contenu -->
    <div class="flex pt-16 overflow-hidden bg-gray-50">
        <!-- Sidebar -->
        @include('partials.sidebar')

        <!-- Contenu principal -->
        <div class="relative w-full h-full overflow-y-auto bg-gray-50 lg:ml-64">
            <main class="p-6">
                <div class="max-w-7xl mx-auto">
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <div class="mb-8">
                            <h2 class="text-2xl font-bold text-gray-800 mb-2">Bonjour, {{ $user->name }} !</h2>
                            <p class="text-gray-600">Bienvenue sur votre tableau de bord.</p>
                        </div>

                        <!-- Cartes de statistiques -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                            <!-- Carte Étudiants -->
                            <div class="bg-blue-50 p-6 rounded-lg shadow">
                                <div class="flex items-center">
                                    <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Étudiants</p>
                                        <p class="text-2xl font-bold text-gray-800">{{ $stats['total_etudiants'] }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Carte Professeurs -->
                            <div class="bg-green-50 p-6 rounded-lg shadow">
                                <div class="flex items-center">
                                    <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Professeurs</p>
                                        <p class="text-2xl font-bold text-gray-800">{{ $stats['total_professeurs'] }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Carte Cours -->
                            <div class="bg-purple-50 p-6 rounded-lg shadow">
                                <div class="flex items-center">
                                    <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Cours</p>
                                        <p class="text-2xl font-bold text-gray-800">{{ $stats['total_cours'] }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Carte Classes -->
                            <div class="bg-yellow-50 p-6 rounded-lg shadow">
                                <div class="flex items-center">
                                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Classes</p>
                                        <p class="text-2xl font-bold text-gray-800">{{ $stats['total_classes'] }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section Événements -->
                        <div class="bg-white rounded-lg shadow p-6">
                            <h3 class="text-lg font-semibold mb-4 text-gray-800">Prochains événements</h3>
                            <div class="space-y-4">
                                @forelse($evenements as $event)
                                    <div class="border-l-4 border-blue-500 pl-4 py-2">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <h4 class="font-medium text-gray-900">{{ $event['titre'] }}</h4>
                                                <p class="text-sm text-gray-600">{{ $event['description'] }}</p>
                                            </div>
                                            <span class="text-sm text-gray-500">{{ $event['date'] }}</span>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-gray-500">Aucun événement à venir.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </main>
            
            <!-- Pied de page -->
            <footer class="bg-white shadow mt-4 p-4">
                <div class="max-w-7xl mx-auto">
                    <p class="text-center text-gray-500 text-sm">
                        &copy; {{ date('Y') }} E-Schoolar. Tous droits réservés.
                    </p>
                </div>
            </footer>
        </div>
    </div>
    
    <!-- Scripts -->
    <script>
        // Gestion du menu mobile
        document.addEventListener('DOMContentLoaded', function() {
            const toggleButton = document.getElementById('toggleSidebar');
            const sidebar = document.getElementById('sidebar');
            const mobileMenuBackdrop = document.getElementById('mobileMenuBackdrop');
            
            if (toggleButton && sidebar) {
                toggleButton.addEventListener('click', function() {
                    sidebar.classList.toggle('-translate-x-full');
                    mobileMenuBackdrop.classList.toggle('hidden');
                });
                
                mobileMenuBackdrop.addEventListener('click', function() {
                    sidebar.classList.add('-translate-x-full');
                    mobileMenuBackdrop.classList.add('hidden');
                });
            }
        });
    </script>
</body>
</html>
