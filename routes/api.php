<?php

use App\Http\Controllers\Api\NoteController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    try {
        DB::connection()->getPdo();
        $dbStatus = 'connected';
    } catch (\Throwable $e) {
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

Route::apiResource('notes', NoteController::class)->only(['index', 'store']);
