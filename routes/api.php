<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Rutas /api/...
Route::any('/', [UserController::class, 'handleRoot']);
Route::any('/get', [UserController::class, 'index']);
Route::any('/create', [UserController::class, 'store']);
Route::any('/login', [UserController::class, 'login']);
Route::any('/update-username', [UserController::class, 'updateUsername']);
Route::any('/update_username', [UserController::class, 'updateUsername']);
Route::any('/update-email', [UserController::class, 'updateEmail']);
Route::any('/update_email', [UserController::class, 'updateEmail']);
Route::any('/update-password', [UserController::class, 'updatePassword']);
Route::any('/update_password', [UserController::class, 'updatePassword']);
Route::any('/delete', [UserController::class, 'destroy']);

Route::prefix('users')->group(function (): void {
    Route::any('/', [UserController::class, 'handleRoot']);
    Route::any('/get', [UserController::class, 'index']);
    Route::any('/create', [UserController::class, 'store']);
    Route::any('/login', [UserController::class, 'login']);
    Route::any('/update-username', [UserController::class, 'updateUsername']);
    Route::any('/update_username', [UserController::class, 'updateUsername']);
    Route::any('/update-email', [UserController::class, 'updateEmail']);
    Route::any('/update_email', [UserController::class, 'updateEmail']);
    Route::any('/update-password', [UserController::class, 'updatePassword']);
    Route::any('/update_password', [UserController::class, 'updatePassword']);
    Route::any('/delete', [UserController::class, 'destroy']);
});

Route::fallback([UserController::class, 'handleRoot']);
