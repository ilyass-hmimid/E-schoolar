<?php

use Illuminate\Support\Facades\Route;

Route::get('/test-error', function () {
    try {
        // Test database connection
        DB::connection()->getPdo();
        
        // Test view rendering
        return view('test');
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});
