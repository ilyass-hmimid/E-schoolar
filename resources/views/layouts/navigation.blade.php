<nav x-data="{ open: false, notificationsOpen: false }" class="navbar navbar-expand-md navbar-light bg-white border-bottom shadow-sm fixed-top">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand d-flex align-items-center" href="{{ route('dashboard') }}">
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
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="nav-link">
                        <i class="fas fa-tachometer-alt me-2"></i>
                        {{ __('Tableau de bord') }}
                    </x-nav-link>
                </li>
                
                @can('viewAny', App\Models\User::class)
                <li class="nav-item">
                    <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')" class="nav-link">
                        <i class="fas fa-users me-2"></i>
                        {{ __('Utilisateurs') }}
                    </x-nav-link>
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
                       :aria-expanded="open">
                        <div class="me-2 d-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10 text-primary" 
                             style="width: 32px; height: 32px; font-size: 14px; font-weight: 500;">
                            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                        </div>
                        <span class="d-none d-md-inline">
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
                        x-transition:leave-end="opacity-0 scale-95">
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                <i class="fas fa-user-circle me-2"></i>
                                {{ __('Profil') }}
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt me-2"></i>
                                    {{ __('Déconnexion') }}
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
