<?php

namespace App\Http\Controllers\Professeur;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DevoirController extends Controller
{
    public function index()
    {
        return Inertia::render('Professeur/Devoirs/Index');
    }

    public function create()
    {
        return Inertia::render('Professeur/Devoirs/Create');
    }

    public function store(Request $request)
    {
        // Logique de création d'un devoir
    }

    public function show($id)
    {
        return Inertia::render('Professeur/Devoirs/Show', [
            'devoirId' => $id
        ]);
    }

    public function edit($id)
    {
        return Inertia::render('Professeur/Devoirs/Edit', [
            'devoirId' => $id
        ]);
    }

    public function update(Request $request, $id)
    {
        // Logique de mise à jour d'un devoir
    }

    public function destroy($id)
    {
        // Logique de suppression d'un devoir
    }
}
