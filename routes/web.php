<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes (Vistas Blade)
|--------------------------------------------------------------------------
*/

// Página Principal: Gestión de Biblioteca de Libros (CRUD y Filtros)
Route::get('/', function () {
    return view('books.index');
})->name('books.home');

// Página de Notas (Demo base arquitectónica inicial)
Route::get('/notes', function () {
    return view('notes.index');
})->name('notes.home');
