<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SimpleDashboardController extends Controller
{
    public function __invoke()
    {
        return view('simple.dashboard', [
            'user' => auth()->user()
        ]);
    }
}
