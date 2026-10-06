<?php

namespace App\Http\Controllers;

use App\Models\Token;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $users = User::take(10)->get();

            return response()->json([
                'success' => true,
                'message' => 'Lista de los 10 primeros usuarios obtenida correctamente.',
                'data' => $users,
            ], 200);
        } catch (\Throwable $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
                'data' => null,
            ], 500);
        }
    }

    public function get(): JsonResponse
    {
        return $this->index();
    }

    public function create(Request $request): JsonResponse
    {
        return $this->store($request);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $data = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
                'password' => ['required', 'string', 'min:8'],
                'username' => ['nullable', 'string', 'max:255', 'unique:users,username'],
            ]);

            $username = $data['username'] ?? explode('@', $data['email'])[0];

            $user = User::create([
                'name' => $data['name'],
                'username' => $username,
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Usuario creado correctamente.',
                'data' => $user,
            ], 201);
        } catch (\Throwable $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
                'data' => null,
            ], 400);
        }
    }

    public function login(Request $request): JsonResponse
    {
        try {
            $data = $request->validate([
                'email' => ['required', 'email', 'max:255'],
                'password' => ['required', 'string', 'min:8'],
            ]);

            $user = User::where('email', $data['email'])->first();

            if (! $user || ! Hash::check($data['password'], $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Credenciales incorrectas.',
                    'data' => null,
                ], 401);
            }

            $oldToken = Token::where('user_id', $user->id)->first();
            if ($oldToken) {
                $oldToken->delete();
            }

            $token = hash('sha256', Str::random(64));

            Token::create([
                'user_id' => $user->id,
                'token' => $token,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Sesión iniciada correctamente.',
                'data' => $token,
            ], 200);
        } catch (\Throwable $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
                'data' => null,
            ], 400);
        }
    }

    public function updateName(Request $request): JsonResponse
    {
        try {
            $token = $request->input('token')
                ?? $request->input('api_key')
                ?? $request->input('apikey')
                ?? $request->bearerToken()
                ?? $request->header('X-Token')
                ?? $request->header('X-Api-Key');
            $name = $request->input('name') ?? $request->input('new_name');

            if (blank($token)) {
                return response()->json([
                    'success' => false,
                    'message' => 'El token es obligatorio.',
                    'data' => null,
                ], 400);
            }

            if (blank($name)) {
                return response()->json([
                    'success' => false,
                    'message' => 'El nuevo nombre es obligatorio.',
                    'data' => null,
                ], 400);
            }

            $tokenRecord = Token::where('token', $token)->first();

            if (! $tokenRecord) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token inválido o expirado.',
                    'data' => null,
                ], 401);
            }

            $user = $tokenRecord->user;

            if (! $user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no encontrado para el token proporcionado.',
                    'data' => null,
                ], 404);
            }

            $user->name = $name;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Nombre actualizado correctamente.',
                'data' => $user,
            ], 200);
        } catch (\Throwable $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
                'data' => null,
            ], 400);
        }
    }

    public function handleRoot(Request $request): JsonResponse
    {
        try {
            $action = strtolower((string) $request->input('action'));

            if ($action === 'login') {
                return $this->login($request);
            }

            if ($action === 'update_name' || $action === 'update-name' || ($request->has('token') && $request->hasAny(['name', 'new_name']))) {
                return $this->updateName($request);
            }

            if ($request->isMethod('get') || $action === 'get') {
                return $this->index();
            }

            return $this->store($request);
        } catch (\Throwable $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
                'data' => null,
            ], 400);
        }
    }
}
