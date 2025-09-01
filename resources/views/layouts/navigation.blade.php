<nav x-data="{ open: false, notificationsOpen: false }" class="navbar navbar-expand-md navbar-light bg-white border-bottom shadow-sm fixed-top">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand d-flex align-items-center" href="{{ 
            auth()->user() && auth()->user()->hasRole('admin') ? route('admin.dashboard') : 
            (auth()->user() && auth()->user()->hasRole('professeur') ? route('professeur.dashboard') : 
            (auth()->user() && auth()->user()->hasRole('assistant') ? route('assistant.dashboard') : 
            (auth()->user() && auth()->user()->hasRole('eleve') ? route('eleve.dashboard') : route('welcome'))))
        }}">
            <x-application-logo class="me-2" style="height: 32px; width: auto;" />
            <span class="d-none d-md-inline fw-bold fs-5 text-dark">{{ config('app.name', 'Allo Tawjih') }}</span>
        </a>
        
        <!-- Mobile menu button -->
        <button class="navbar-toggler" type="button" @click="open = !open" :aria-expanded="open">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <!-- Navigation Links -->
        <div class="collapse navbar-collapse" :class="{'show': open}">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <x-nav-link :href="auth()->user() && auth()->user()->hasRole('admin') ? route('admin.dashboard') : 
                        (auth()->user() && auth()->user()->hasRole('professeur') ? route('professeur.dashboard') : 
                        (auth()->user() && auth()->user()->hasRole('assistant') ? route('assistant.dashboard') : 
                        (auth()->user() && auth()->user()->hasRole('eleve') ? route('eleve.dashboard') : route('welcome'))))" 
                        :active="request()->routeIs('admin.dashboard') || request()->routeIs('professeur.dashboard') || request()->routeIs('assistant.dashboard') || request()->routeIs('eleve.dashboard')" class="nav-link">
                        <i class="fas fa-tachometer-alt me-2"></i>
                        {{ __('Tableau de bord') }}
                    </x-nav-link>
                </li>
                
                @can('viewAny', App\Models\User::class)
                <li class="nav-item dropdown" x-data="{ open: false }">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" 
                       href="#" 
                       @click.prevent="open = !open"
                       :class="{ 'active': window.location.href.includes('/admin/administrateurs') || window.location.href.includes('/admin/assistants') || window.location.href.includes('/admin/eleves') || window.location.href.includes('/admin/enseignants') }"
                       :aria-expanded="open">
                        <i class="fas fa-users-cog me-2"></i>
                        {{ __('Gestion Utilisateurs') }}
                    </a>
                    
                    <!-- Dropdown menu -->
                    <ul class="dropdown-menu shadow-sm" 
                        x-show="open" 
                        @click.away="open = false"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95">
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.administrateurs.index') }}" :class="{ 'active': window.location.href.includes('/admin/administrateurs') }">
                                <i class="fas fa-user-shield me-2"></i>
                                {{ __('Administrateurs') }}
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.enseignants.index') }}" :class="{ 'active': window.location.href.includes('/admin/enseignants') }">
                                <i class="fas fa-chalkboard-teacher me-2"></i>
                                {{ __('Enseignants') }}
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.assistants.index') }}" :class="{ 'active': window.location.href.includes('/admin/assistants') }">
                                <i class="fas fa-user-tie me-2"></i>
                                {{ __('Assistants') }}
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.eleves.index') }}" :class="{ 'active': window.location.href.includes('/admin/eleves') }">
                                <i class="fas fa-user-graduate me-2"></i>
                                {{ __('Élèves') }}
                            </a>
                        </li>
                    </ul>
                </li>
                @endcan
            </ul>

            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <!-- Notifications -->
                <li class="nav-item dropdown" x-data="{ open: false }">
                    <a class="nav-link position-relative" href="#" @click.prevent="open = !open">
                        <i class="fas fa-bell"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            0
                            <span class="visually-hidden">notifications non lues</span>
                        </span>
                    </a>
                    
                    <!-- Notification Dropdown -->
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" 
                        x-show="open" 
                        @click.away="open = false"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95">
                        <li class="dropdown-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Notifications</h6>
                                <button type="button" class="btn-close" @click="open = false"></button>
                            </div>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li class="text-center py-3 text-muted">
                            Aucune nouvelle notification
                        </li>
                    </ul>
                </li>

                <!-- User Dropdown -->
                <li class="nav-item dropdown" x-data="{ open: false }">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" 
                       href="#" 
                       @click.prevent="open = !open"
                       :aria-expanded="open"
                       style="cursor: pointer;">
                        <div class="me-2 d-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10 text-primary" 
                             style="width: 32px; height: 32px; font-size: 14px; font-weight: 500; flex-shrink: 0;">
                            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                        </div>
                        <span class="d-none d-md-inline text-truncate" style="max-width: 150px;">
                            {{ Auth::user()->name }}
                        </span>
                    </a>
                    
                    <!-- Dropdown menu -->
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" 
                        x-show="open" 
                        @click.away="open = false"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        style="min-width: 200px; border-radius: 8px;">
                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="{{ route('profile.edit') }}">
                                <i class="fas fa-user-circle me-2"></i>
                                <span>{{ __('Profil') }}</span>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" class="w-100">
                                @csrf
                                <button type="submit" class="dropdown-item logout-btn">
                                    <i class="fas fa-sign-out-alt"></i>
                                    <span>{{ __('Déconnexion') }}</span>
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
