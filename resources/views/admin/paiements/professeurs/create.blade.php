@extends('layouts.admin')

@section('title', 'Nouveau Paiement Enseignant')

@section('content')
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Tableau de bord</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.paiements.professeurs.index') }}">Paiements des Enseignants</a></li>
            <li class="breadcrumb-item active" aria-current="page">Nouveau Paiement</li>
        </ol>
    </nav>

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Nouveau Paiement Enseignant</h1>
    </div>

    <form action="{{ route('admin.paiements.professeurs.store') }}" method="POST">
        @csrf
        @include('admin.paiements.professeurs._form')
    </form>
</div>
@endsection
