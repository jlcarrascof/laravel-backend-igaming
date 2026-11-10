<?php

use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\NoteController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (Prefijo /api/v1 configurado en bootstrap/app.php)
|--------------------------------------------------------------------------
*/

Route::get('/health', function () {
    try {
        DB::connection()->getPdo();
        $dbStatus = 'connected';
    } catch (Throwable $e) {
        $dbStatus = 'disconnected';
    }

    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toIso8601String(),
        'services' => [
            'database' => $dbStatus,
        ],
    ], 200);
});

// Rutas de Biblioteca (Books & Categories)
Route::apiResource('categories', CategoryController::class)->only(['index', 'store', 'destroy']);
Route::apiResource('books', BookController::class);

// Recurso Note neutro previo
Route::apiResource('notes', NoteController::class)->only(['index', 'store']);
