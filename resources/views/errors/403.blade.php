@extends('errors.layout')

@section('title', 'Accès refusé')

@section('content')
    <div class="error-code">403</div>
    <h1 class="error-title">Accès refusé</h1>
    <p class="error-message">
        Désolé, vous n'avez pas l'autorisation d'accéder à cette page.
    </p>
    <div class="error-actions">
        <a href="{{ url()->previous() }}" class="btn btn-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L7.414 9H15a1 1 0 110 2H7.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Retour
        </a>
        <a href="{{ route('home') }}" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
            </svg>
            Page d'accueil
        </a>
    </div>
@endsection
