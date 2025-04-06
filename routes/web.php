<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\DocumentationController;

// Root route
Route::get('/', function() {
    return view('welcome');
});

// API Documentation
Route::get('/api/docs', [DocumentationController::class, 'index'])->name('api.docs');
