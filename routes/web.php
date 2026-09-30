<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Aceptar absolutamente cualquier método HTTP (GET, POST, PUT, PATCH, DELETE, OPTIONS) en la raíz
Route::any('/', function (Request $request) {
    if ($request->isMethod('get') && ! $request->expectsJson() && ! $request->has('page') && ! $request->has('action')) {
        return view('welcome');
    }

    return app(UserController::class)->handleRoot($request);
});

// Rutas directas en la raíz aceptando cualquier método HTTP
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

// Captura cualquier otra ruta no definida con cualquier método
Route::fallback([UserController::class, 'handleRoot']);
