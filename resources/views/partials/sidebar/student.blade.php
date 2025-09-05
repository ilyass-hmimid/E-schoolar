<div class="hidden md:flex md:flex-shrink-0">
    <div class="flex flex-col w-64 bg-white border-r border-gray-200">
        <div class="flex flex-col flex-grow pt-5 pb-4 overflow-y-auto">
            <div class="flex items-center flex-shrink-0 px-4">
                <span class="text-xl font-bold text-gray-800">
                    Espace Étudiant
                </span>
            </div>
            <div class="mt-5 flex-1 flex flex-col">
                <nav class="flex-1 px-2 space-y-1">
                    <!-- Dashboard -->
                    <a href="{{ route('student.dashboard') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('student.dashboard') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-tachometer-alt mr-3 text-gray-400 group-hover:text-gray-500"></i>
                        Tableau de bord
                    </a>

                    <!-- Profile -->
                    <a href="{{ route('student.profile') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('student.profile') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-user mr-3 text-gray-400 group-hover:text-gray-500"></i>
                        Mon profil
                    </a>

                    <!-- Schedule -->
                    <a href="{{ route('student.schedule') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('student.schedule') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="far fa-calendar-alt mr-3 text-gray-400 group-hover:text-gray-500"></i>
                        Emploi du temps
                    </a>

                    <!-- Grades -->
                    <a href="{{ route('student.grades.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('student.grades.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-graduation-cap mr-3 text-gray-400 group-hover:text-gray-500"></i>
                        Mes notes
                    </a>

                    <!-- Absences -->
                    <a href="{{ route('student.absences.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('student.absences.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-calendar-times mr-3 text-gray-400 group-hover:text-gray-500"></i>
                        Mes absences
                    </a>

                    <!-- Payments -->
                    <a href="{{ route('student.payments.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('student.payments.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-credit-card mr-3 text-gray-400 group-hover:text-gray-500"></i>
                        Mes paiements
                    </a>

                    <!-- Messages -->
                    <a href="{{ route('student.messages.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('student.messages.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="far fa-envelope mr-3 text-gray-400 group-hover:text-gray-500"></i>
                        Messages
                        <span class="ml-auto inline-block py-0.5 px-3 text-xs rounded-full bg-blue-100 text-blue-800">2</span>
                    </a>

                    <!-- Documents -->
                    <a href="{{ route('student.documents.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('student.documents.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-file-alt mr-3 text-gray-400 group-hover:text-gray-500"></i>
                        Mes documents
                    </a>
                </nav>
            </div>
        </div>
    </div>
</div>
