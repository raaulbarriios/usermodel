<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::any('/', function (Request $request) {
    if ($request->isMethod('get') && ! $request->expectsJson() && ! $request->has('page') && ! $request->has('action')) {
        return view('welcome');
    }

    return app(UserController::class)->handleRoot($request);
});

Route::any('/get', [UserController::class, 'index']);
Route::any('/create', [UserController::class, 'store']);
Route::any('/login', [UserController::class, 'login']);
Route::any('/update_name', [UserController::class, 'updateName']);
Route::any('/update-name', [UserController::class, 'updateName']);

Route::fallback([UserController::class, 'handleRoot']);
