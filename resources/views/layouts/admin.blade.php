<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Allo Tawjih') }} - @yield('title')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Admin Styles -->
    <link href="{{ asset('css/admin-styles.css') }}" rel="stylesheet">
    
    @stack('styles')
</head>
<body class="bg-dark-950">
    <!-- Mobile Menu Button -->
    <button id="mobileMenuBtn" class="md:hidden fixed top-4 left-4 z-50 bg-dark-800 p-3 rounded-xl text-white shadow-lg">
        <i class="fas fa-bars text-lg"></i>
    </button>

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="p-6 border-b border-dark-800">
            <a href="{{ route('admin.dashboard') }}" class="text-2xl font-bold text-white">
                <span class="text-primary">Allo</span> Tawjih
            </a>
        </div>
        
        <nav class="p-4">
            <p class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                Tableau de bord
            </p>
            
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i>
                <span>Tableau de bord</span>
            </a>
            
            <p class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider mt-6 mb-2">
                Gestion des utilisateurs
            </p>
            
            <a href="{{ route('admin.eleves.index') }}" class="nav-link {{ request()->routeIs('admin.eleves.*') ? 'active' : '' }}">
                <i class="fas fa-user-graduate"></i>
                <span>Élèves</span>
            </a>
            
            <a href="{{ route('admin.professeurs.index') }}" class="nav-link {{ request()->routeIs('admin.professeurs.*') ? 'active' : '' }}">
                <i class="fas fa-chalkboard-teacher"></i>
                <span>Professeurs</span>
            </a>
            
            <a href="{{ route('admin.assistants.index') }}" class="nav-link {{ request()->routeIs('admin.assistants.*') ? 'active' : '' }}">
                <i class="fas fa-user-tie"></i>
                <span>Assistants</span>
            </a>
            
            <a href="{{ route('admin.administrateurs.index') }}" class="nav-link {{ request()->routeIs('admin.administrateurs.*') ? 'active' : '' }}">
                <i class="fas fa-user-shield"></i>
                <span>Administrateurs</span>
            </a>
            
            <p class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider mt-6 mb-2">
                Gestion pédagogique
            </p>
            
            <a href="{{ route('admin.cours.index') }}" class="nav-link {{ request()->routeIs('admin.cours.*') ? 'active' : '' }}">
                <i class="fas fa-book"></i>
                <span>Cours / Matières</span>
            </a>
            
            <a href="{{ route('admin.classes.index') }}" class="nav-link {{ request()->routeIs('admin.classes.*') ? 'active' : '' }}">
                <i class="fas fa-chalkboard"></i>
                <span>Classes / Groupes</span>
            </a>
            
            <a href="{{ route('admin.emplois-du-temps.index') }}" class="nav-link {{ request()->routeIs('admin.emplois-du-temps.*') ? 'active' : '' }}">
                <i class="far fa-calendar-alt"></i>
                <span>Emplois du temps</span>
            </a>
            
            <a href="{{ route('admin.examens.index') }}" class="nav-link {{ request()->routeIs('admin.examens.*') ? 'active' : '' }}">
                <i class="fas fa-file-alt"></i>
                <span>Examens & Notes</span>
            </a>
            
            <a href="{{ route('admin.absences.index') }}" class="nav-link {{ request()->routeIs('admin.absences.*') ? 'active' : '' }}">
                <i class="far fa-calendar-times"></i>
                <span>Gestion des absences</span>
                @php
                    $pendingAbsencesCount = \App\Models\Absence::where('justifiee', false)
                        ->where('date_absence', '>=', now()->subDays(30))
                        ->count();
                @endphp
                @if($pendingAbsencesCount > 0)
                    <span class="absolute right-4 inline-flex items-center justify-center h-5 w-5 text-xs font-bold text-white bg-red-500 rounded-full">
                        {{ $pendingAbsencesCount }}
                    </span>
                @endif
            </a>
            
            <p class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider mt-6 mb-2">
                Gestion administrative
            </p>
            
            
            <a href="{{ route('admin.paiements.index') }}" class="nav-link {{ request()->routeIs('admin.paiements.*') ? 'active' : '' }}">
                <i class="fas fa-money-bill-wave"></i>
                <span>Paiements</span>
            </a>
            
            <a href="{{ route('admin.salaires.index') }}" class="nav-link {{ request()->routeIs('admin.salaires.*') ? 'active' : '' }}">
                <i class="fas fa-euro-sign"></i>
                <span>Salaires</span>
            </a>
            
            <a href="{{ route('admin.rapports.index') }}" class="nav-link {{ request()->routeIs('admin.rapports.*') ? 'active' : '' }}">
                <i class="fas fa-chart-bar"></i>
                <span>Rapports</span>
            </a>
            
            <p class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider mt-6 mb-2">
                Administration
            </p>
            
            <a href="{{ route('admin.parametres.index') }}" class="nav-link {{ request()->routeIs('admin.parametres.*') ? 'active' : '' }}">
                <i class="fas fa-cog"></i>
                <span>Paramètres généraux</span>
            </a>
            
            <a href="{{ route('admin.roles.index') }}" class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                <i class="fas fa-user-shield"></i>
                <span>Rôles & Permissions</span>
            </a>
        </nav>
        
        <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-dark-800">
            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <button type="submit" class="w-full flex items-center px-4 py-3 text-red-400 hover:bg-red-500/10 hover:text-red-300 rounded-lg transition-colors duration-200">
                    <i class="fas fa-sign-out-alt mr-3"></i>
                    <span>Déconnexion</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content bg-dark-950">
        <!-- Top Bar -->
        <div class="flex justify-between items-center p-6 border-b border-dark-800">
            <div>
                <h1 class="text-2xl font-bold text-white">@yield('title', 'Tableau de bord')</h1>
                <p class="text-gray-400">@yield('subtitle', 'Bienvenue dans votre espace d\'administration')</p>
            </div>
            
            <div class="flex items-center space-x-4">
                <!-- Notifications -->
                <button class="relative p-2 text-gray-400 hover:text-white hover:bg-dark-800 rounded-lg transition-colors duration-200">
                    <i class="fas fa-bell text-xl"></i>
                    <span class="absolute top-0 right-0 h-5 w-5 bg-red-500 rounded-full text-xs flex items-center justify-center text-white">3</span>
                </button>
                
                <!-- User Menu -->
                <div class="relative">
                    <button id="userMenuButton" class="flex items-center space-x-2 p-2 rounded-lg hover:bg-dark-800 transition-colors duration-200">
                        <div class="h-10 w-10 rounded-full bg-primary/20 flex items-center justify-center text-primary font-bold">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div class="text-left hidden md:block">
                            <p class="text-sm font-medium text-white">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-400">Administrateur</p>
                        </div>
                        <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        <div class="px-6 pt-6">
            @if(session('success'))
                <div class="bg-green-900/50 border-l-4 border-green-500 text-green-100 p-4 mb-6 rounded-r-lg" role="alert">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="ml-3">
                            <p class="font-medium">Succès</p>
                            <p class="text-sm">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-900/50 border-l-4 border-red-500 text-red-100 p-4 mb-6 rounded-r-lg" role="alert">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                        <div class="ml-3">
                            <p class="font-medium">Erreur</p>
                            <ul class="list-disc list-inside text-sm">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Page Content -->
        <div class="p-6">
            @yield('content')
        </div>
    </main>

    @stack('scripts')
    
    <script>
        // Toggle mobile menu
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const sidebar = document.querySelector('.sidebar');
        const menuIcon = mobileMenuBtn?.querySelector('i');
        
        if (mobileMenuBtn && sidebar && menuIcon) {
            mobileMenuBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                sidebar.classList.toggle('active');
                menuIcon.classList.toggle('fa-bars');
                menuIcon.classList.toggle('fa-times');
            });
            
            // Close mobile menu when clicking outside
            document.addEventListener('click', (e) => {
                if (!sidebar.contains(e.target) && e.target !== mobileMenuBtn) {
                    sidebar.classList.remove('active');
                    menuIcon.classList.add('fa-bars');
                    menuIcon.classList.remove('fa-times');
                }
            });
        }
        
        // Toggle user dropdown
        const userMenuButton = document.getElementById('userMenuButton');
        const userMenu = document.getElementById('userMenu');
        
        if (userMenuButton && userMenu) {
            userMenuButton.addEventListener('click', (e) => {
                e.stopPropagation();
                userMenu.classList.toggle('hidden');
            });
            
            // Close when clicking outside
            document.addEventListener('click', () => {
                if (userMenu && !userMenu.classList.contains('hidden')) {
                    userMenu.classList.add('hidden');
                }
            });
        }

        // Close dropdown when clicking on a link
        document.querySelectorAll('.dropdown-item').forEach(item => {
            item.addEventListener('click', () => {
                if (userMenu) {
                    userMenu.classList.add('hidden');
                }
            });
        });
    </script>
</body>
</html>
