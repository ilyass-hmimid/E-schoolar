@extends('layouts.app')

@section('title', 'Mes Devoirs')

@section('header')
<div class="flex justify-between items-center">
    <h1 class="text-2xl font-semibold text-gray-900">
        Mes Devoirs
    </h1>
    
    <div class="flex space-x-3">
        <a href="{{ route('eleve.devoirs.index', ['filter' => 'a_rendre']) }}" 
           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white {{ request('filter') !== 'rendus' ? 'bg-indigo-600 hover:bg-indigo-700' : 'bg-gray-300 hover:bg-gray-400' }}">
            À rendre
        </a>
        <a href="{{ route('eleve.devoirs.index', ['filter' => 'rendus']) }}" 
           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white {{ request('filter') === 'rendus' ? 'bg-indigo-600 hover:bg-indigo-700' : 'bg-gray-300 hover:bg-gray-400' }}">
            Déjà rendus
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="mb-8">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-medium text-gray-900">
            {{ request('filter') === 'rendus' ? 'Devoirs rendus' : 'Devoirs à rendre' }}
        </h2>
        
        <div class="relative">
            <select onchange="window.location.href=this.value" class="block appearance-none bg-white border border-gray-300 text-gray-700 py-2 px-4 pr-8 rounded leading-tight focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="{{ route('eleve.devoirs.index', ['filter' => request('filter'), 'tri' => 'date_limite_asc']) }}" {{ request('tri') === 'date_limite_asc' ? 'selected' : '' }}>Tri: Date limite (plus proche)</option>
                <option value="{{ route('eleve.devoirs.index', ['filter' => request('filter'), 'tri' => 'date_limite_desc']) }}" {{ request('tri') === 'date_limite_desc' ? 'selected' : '' }}>Tri: Date limite (plus éloignée)</option>
                <option value="{{ route('eleve.devoirs.index', ['filter' => request('filter'), 'tri' => 'matiere']) }}" {{ request('tri') === 'matiere' ? 'selected' : '' }}>Tri: Matière</option>
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                <svg class="fill-current h-4 w-4" viewBox="0 0 20 20">
                    <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                </svg>
            </div>
        </div>
    </div>
    
    @if($devoirs->count() > 0)
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <ul class="divide-y divide-gray-200">
                @foreach($devoirs as $devoir)
                    <li class="hover:bg-gray-50">
                        <div class="px-4 py-4 sm:px-6">
                            <div class="flex items-center justify-between">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center">
                                        <p class="text-sm font-medium text-indigo-600">
                                            {{ $devoir->titre }}
                                        </p>
                                        @if($devoir->date_limite->isToday() && !$devoir->devoirRendu)
                                            <span class="ml-2 text-xs px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-800">
                                                Aujourd'hui
                                            </span>
                                        @elseif($devoir->date_limite->isPast() && !$devoir->devoirRendu)
                                            <span class="ml-2 text-xs px-2 py-0.5 rounded-full bg-red-100 text-red-800">
                                                En retard
                                            </span>
                                        @elseif($devoir->devoirRendu)
                                            <span class="ml-2 text-xs px-2 py-0.5 rounded-full bg-green-100 text-green-800">
                                                Rendu
                                                @if($devoir->devoirRendu->est_en_retard)
                                                    (en retard)
                                                @endif
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <div class="mt-2 flex flex-wrap gap-4 text-sm text-gray-500">
                                        <div class="flex items-center">
                                            <svg class="h-4 w-4 mr-1 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                            </svg>
                                            {{ $devoir->date_limite->format('d/m/Y') }}
                                        </div>
                                        
                                        @if($devoir->matiere)
                                        <div class="flex items-center">
                                            <svg class="h-4 w-4 mr-1 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M10.394 2.08a1 1 0 01-.788 0l-7 3a1 1 0 010 1.84l5.25 2.35a1 1 0 00.9 0l5.25-2.35a1 1 0 010-1.84l-7-3zM3.31 9.397l5.25 2.35a1 1 0 00.9 0l5.25-2.35a1 1 0 01.69.95v5.4a1 1 0 01-1.09 1l-5.25-1.35a1 1 0 00-.62 0l-5.25 1.35A1 1 0 012.62 15.75v-5.4a1 1 0 01.69-.953z" />
                                            </svg>
                                            {{ $devoir->matiere->nom }}
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="ml-4">
                                    <a href="{{ route('eleve.devoirs.show', $devoir->id) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-full shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                                        Voir
                                    </a>
                                </div>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
            
            <div class="px-4 py-3 border-t border-gray-200">
                {{ $devoirs->links() }}
            </div>
        </div>
    @else
        <div class="text-center py-12 bg-white shadow sm:rounded-lg">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">
                @if(request('filter') === 'rendus')
                    Aucun devoir rendu
                @else
                    Aucun devoir à rendre
                @endif
            </h3>
        </div>
    @endif
</div>

@if($devoirsProches->count() > 0 && request('filter') !== 'rendus')
<div class="mt-8">
    <h2 class="text-lg font-medium text-gray-900 mb-4">Prochains devoirs</h2>
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <ul class="divide-y divide-gray-200">
            @foreach($devoirsProches as $devoir)
                <li class="px-6 py-4 hover:bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-indigo-600">
                                {{ $devoir->titre }}
                            </p>
                            <p class="mt-1 text-sm text-gray-500">
                                {{ $devoir->matiere->nom }} - Pour le {{ $devoir->date_limite->format('d/m/Y') }}
                            </p>
                        </div>
                        <a href="{{ route('eleve.devoirs.show', $devoir->id) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">
                            Voir
                        </a>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
</div>
@endif
@endsection
