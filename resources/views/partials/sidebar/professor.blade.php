<div class="hidden md:flex md:flex-shrink-0">
    <div class="flex flex-col w-64 bg-white border-r border-gray-200">
        <div class="flex flex-col flex-grow pt-5 pb-4 overflow-y-auto">
            <div class="flex items-center flex-shrink-0 px-4">
                <span class="text-xl font-bold text-gray-800">
                    Professeur
                </span>
            </div>
            <div class="mt-5 flex-1 flex flex-col">
                <nav class="flex-1 px-2 space-y-1">
                    <!-- Dashboard -->
                    <a href="{{ route('professor.dashboard') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('professor.dashboard') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-tachometer-alt mr-3 text-gray-400 group-hover:text-gray-500"></i>
                        Tableau de bord
                    </a>

                    <!-- My Classes -->
                    <a href="{{ route('professor.classes.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('professor.classes.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-chalkboard-teacher mr-3 text-gray-400 group-hover:text-gray-500"></i>
                        Mes classes
                    </a>

                    <!-- Students -->
                    <a href="{{ route('professor.students.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('professor.students.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-user-graduate mr-3 text-gray-400 group-hover:text-gray-500"></i>
                        Mes étudiants
                    </a>

                    <!-- Absences -->
                    <a href="{{ route('professor.absences.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('professor.absences.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-calendar-times mr-3 text-gray-400 group-hover:text-gray-500"></i>
                        Gestion des absences
                    </a>

                    <!-- Grades -->
                    <a href="{{ route('professor.grades.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('professor.grades.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-graduation-cap mr-3 text-gray-400 group-hover:text-gray-500"></i>
                        Saisie des notes
                    </a>

                    <!-- Schedule -->
                    <a href="{{ route('professor.schedule') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('professor.schedule') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="far fa-calendar-alt mr-3 text-gray-400 group-hover:text-gray-500"></i>
                        Emploi du temps
                    </a>

                    <!-- Messages -->
                    <a href="{{ route('professor.messages.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('professor.messages.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="far fa-envelope mr-3 text-gray-400 group-hover:text-gray-500"></i>
                        Messages
                        <span class="ml-auto inline-block py-0.5 px-3 text-xs rounded-full bg-blue-100 text-blue-800">3</span>
                    </a>
                </nav>
            </div>
        </div>
    </div>
</div>
