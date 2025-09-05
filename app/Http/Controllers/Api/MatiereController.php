<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MatiereController extends Controller
{
    public function index()
    {
        return response()->json(['message' => 'Liste des matières']);
    }
}
