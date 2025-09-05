<nav class="bg-white shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="text-xl font-bold text-gray-800">
                        {{ config('app.name') }}
                    </a>
                </div>
                <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                    <a href="{{ route('home') }}" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                        Accueil
                    </a>
                    @auth
                        @if(auth()->user()->hasRole('admin'))
                            <a href="{{ route('admin.dashboard') }}" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Tableau de bord
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
            <div class="hidden sm:ml-6 sm:flex sm:items-center">
                @guest
                    <a href="{{ route('login') }}" class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium">
                        Connexion
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="ml-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Inscription
                        </a>
                    @endif
                @else
                    <div class="ml-3 relative">
                        <div class="flex items-center">
                            <span class="text-gray-700 mr-4">
                                {{ Auth::user()->name }}
                            </span>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium">
                                    Déconnexion
                                </button>
                            </form>
                        </div>
                    </div>
                @endguest
            </div>
            <!-- Mobile menu button -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button type="button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500" aria-controls="mobile-menu" aria-expanded="false">
                    <span class="sr-only">Open main menu</span>
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div class="sm:hidden" id="mobile-menu">
        <div class="pt-2 pb-3 space-y-1">
            <a href="{{ route('home') }}" class="border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                Accueil
            </a>
            @auth
                @if(auth()->user()->hasRole('admin'))
                    <a href="{{ route('admin.dashboard') }}" class="border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                        Tableau de bord
                    </a>
                @endif
                <form method="POST" action="{{ route('logout') }}" class="border-t border-gray-200 pt-4 mt-4">
                    @csrf
                    <button type="submit" class="text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700 block pl-3 pr-4 py-2 text-base font-medium w-full text-left">
                        Déconnexion
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                    Connexion
                </a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                        Inscription
                    </a>
                @endif
            @endauth
        </div>
    </div>
</nav>

@push('scripts')
<script>
    // Toggle mobile menu
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuButton = document.querySelector('[aria-controls="mobile-menu"]');
        const mobileMenu = document.getElementById('mobile-menu');
        
        mobileMenuButton.addEventListener('click', function() {
            const expanded = mobileMenuButton.getAttribute('aria-expanded') === 'true' || false;
            mobileMenuButton.setAttribute('aria-expanded', !expanded);
            mobileMenu.classList.toggle('hidden');
        });
    });
</script>
@endpush
