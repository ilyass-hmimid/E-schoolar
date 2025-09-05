<div class="hidden md:flex md:flex-shrink-0">
    <div class="flex flex-col w-64 bg-white border-r border-gray-200">
        <div class="flex flex-col flex-grow pt-5 pb-4 overflow-y-auto">
            <div class="flex items-center flex-shrink-0 px-4">
                <span class="text-xl font-bold text-gray-800">
                    Administration
                </span>
            </div>
            <div class="mt-5 flex-1 flex flex-col">
                <nav class="flex-1 px-2 space-y-1">
                    <!-- Dashboard -->
                    <a href="{{ route('admin.dashboard') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.dashboard') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-tachometer-alt mr-3 text-gray-400 group-hover:text-gray-500"></i>
                        Tableau de bord
                    </a>

                    <!-- Users Management -->
                    <a href="{{ route('admin.users.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.users.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-users mr-3 text-gray-400 group-hover:text-gray-500"></i>
                        Gestion des utilisateurs
                    </a>

                    <!-- Payments -->
                    <a href="{{ route('admin.payments.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.payments.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-credit-card mr-3 text-gray-400 group-hover:text-gray-500"></i>
                        Gestion des paiements
                    </a>

                    <!-- Absences -->
                    <a href="{{ route('admin.absences.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.absences.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-calendar-times mr-3 text-gray-400 group-hover:text-gray-500"></i>
                        Gestion des absences
                    </a>

                    <!-- Classes -->
                    <a href="{{ route('admin.classes.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.classes.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-chalkboard mr-3 text-gray-400 group-hover:text-gray-500"></i>
                        Gestion des classes
                    </a>

                    <!-- Subjects -->
                    <a href="{{ route('admin.subjects.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.subjects.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-book mr-3 text-gray-400 group-hover:text-gray-500"></i>
                        Matières
                    </a>

                    <!-- Settings -->
                    <a href="{{ route('admin.settings') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.settings') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-cog mr-3 text-gray-400 group-hover:text-gray-500"></i>
                        Paramètres
                    </a>
                </nav>
            </div>
        </div>
    </div>
</div>
