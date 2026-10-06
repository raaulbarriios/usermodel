<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/get', [UserController::class, 'index']);
Route::post('/create', [UserController::class, 'store']);
Route::post('/login', [UserController::class, 'login']);
Route::match(['put', 'post', 'patch'], '/update_name', [UserController::class, 'updateName']);
Route::match(['put', 'post', 'patch'], '/update-name', [UserController::class, 'updateName']);

Route::any('/', [UserController::class, 'handleRoot']);
Route::any('/get', [UserController::class, 'index']);
Route::any('/create', [UserController::class, 'store']);
Route::any('/login', [UserController::class, 'login']);
Route::any('/update_name', [UserController::class, 'updateName']);
Route::any('/update-name', [UserController::class, 'updateName']);

Route::prefix('users')->group(function (): void {
    Route::any('/', [UserController::class, 'handleRoot']);
    Route::any('/get', [UserController::class, 'index']);
    Route::any('/create', [UserController::class, 'store']);
    Route::any('/login', [UserController::class, 'login']);
    Route::any('/update_name', [UserController::class, 'updateName']);
    Route::any('/update-name', [UserController::class, 'updateName']);
});

Route::fallback([UserController::class, 'handleRoot']);
