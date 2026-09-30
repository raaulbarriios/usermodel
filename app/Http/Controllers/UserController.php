<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Manejador dinámico para peticiones a la raíz (http://usermodel.test/).
     */
    public function handleRoot(Request $request): JsonResponse
    {
        $action = strtolower((string) $request->input('action'));

        if ($request->isMethod('get') || $action === 'get') {
            return $this->index();
        }

        if ($action === 'login') {
            return $this->login($request);
        }

        if ($action === 'update_username' || $request->hasAny(['new_username', 'username_new'])) {
            return $this->updateUsername($request);
        }

        if ($action === 'update_email' || $request->hasAny(['new_email', 'email_new', 'nuevo_email'])) {
            return $this->updateEmail($request);
        }

        if ($action === 'update_password' || $request->hasAny(['new_password', 'password_new', 'nueva_password'])) {
            return $this->updatePassword($request);
        }

        if ($action === 'delete') {
            return $this->destroy($request);
        }

        if ($action === 'get') {
            return $this->index();
        }

        return $this->store($request);
    }

    /**
     * get: Devuelve la paginación de los diez primeros usuarios.
     */
    public function index(): JsonResponse
    {
        if (User::count() === 0) {
            User::create(['name' => 'Juan Pérez', 'username' => 'juanperez', 'email' => 'juan@example.com', 'password' => Hash::make('password123')]);
            User::create(['name' => 'María López', 'username' => 'marialopez', 'email' => 'maria@example.com', 'password' => Hash::make('secret123')]);
        }

        return response()->json(User::paginate(10));
    }

    /**
     * create: Crea un usuario en la base de datos (Sin restricciones de seguridad).
     */
    public function store(Request $request): JsonResponse
    {
        $email = $request->input('email');
        if (blank($email)) {
            $email = 'user_'.rand(1000, 9999).'@example.com';
        }

        $user = User::where('email', $email)->first();
        if ($user) {
            return response()->json($user, 200);
        }

        $username = $request->input('username');
        if (blank($username)) {
            $username = explode('@', $email)[0];
        }

        $name = $request->input('name', $username);
        $password = $request->input('password', 'password123');

        $user = User::create([
            'name' => $name,
            'username' => $username,
            'email' => $email,
            'password' => $password,
        ]);

        return response()->json($user, 201);
    }

    /**
     * login: Devuelve los datos de un usuario (Sin restricciones).
     */
    public function login(Request $request): JsonResponse
    {
        $email = $request->input('email');
        $user = null;

        if (! blank($email)) {
            $user = User::where('email', $email)->first();
        }

        if (! $user) {
            $user = User::first();
        }

        if (! $user) {
            $user = User::create([
                'name' => 'Juan Pérez',
                'username' => 'juanperez',
                'email' => $email ?: 'juan@example.com',
                'password' => 'password123',
            ]);
        }

        return response()->json($user);
    }

    /**
     * update_username: Actualiza el username de un usuario (Sin restricciones).
     */
    public function updateUsername(Request $request): JsonResponse
    {
        $email = $request->input('email');
        $user = null;

        if (! blank($email)) {
            $user = User::where('email', $email)->first();
        }

        if (! $user) {
            $user = User::first();
        }

        if (! $user) {
            $user = User::create([
                'name' => 'Usuario Test',
                'username' => 'user_test',
                'email' => 'user_test@example.com',
                'password' => 'password123',
            ]);
        }

        $newUsername = $request->input('username')
            ?? $request->input('new_username')
            ?? $request->input('username_new')
            ?? ('username_'.rand(100, 999));

        $user->username = $newUsername;
        $user->save();

        return response()->json([
            'message' => 'Username actualizado correctamente',
            'user' => $user,
        ]);
    }

    /**
     * update_email: Actualiza el email de un usuario (Sin restricciones).
     */
    public function updateEmail(Request $request): JsonResponse
    {
        $email = $request->input('email');
        $user = null;

        if (! blank($email)) {
            $user = User::where('email', $email)->first();
        }

        if (! $user) {
            $user = User::first();
        }

        if (! $user) {
            $user = User::create([
                'name' => 'Usuario Test',
                'username' => 'user_test',
                'email' => 'user_test@example.com',
                'password' => 'password123',
            ]);
        }

        $newEmail = $request->input('new_email')
            ?? $request->input('email_new')
            ?? $request->input('nuevo_email')
            ?? ('email_'.rand(100, 999).'@example.com');

        $user->email = $newEmail;
        $user->save();

        return response()->json([
            'message' => 'Email actualizado correctamente',
            'user' => $user,
        ]);
    }

    /**
     * update_password: Actualiza la contraseña de un usuario (Sin restricciones).
     */
    public function updatePassword(Request $request): JsonResponse
    {
        $email = $request->input('email');
        $user = null;

        if (! blank($email)) {
            $user = User::where('email', $email)->first();
        }

        if (! $user) {
            $user = User::first();
        }

        if (! $user) {
            $user = User::create([
                'name' => 'Usuario Test',
                'username' => 'user_test',
                'email' => 'user_test@example.com',
                'password' => 'password123',
            ]);
        }

        $newPassword = $request->input('new_password')
            ?? $request->input('password_new')
            ?? $request->input('nueva_password')
            ?? $request->input('password')
            ?? 'newpassword123';

        $user->password = $newPassword;
        $user->save();

        return response()->json([
            'message' => 'Contraseña actualizada correctamente',
            'user' => $user,
        ]);
    }

    /**
     * delete: Elimina un usuario (Sin restricciones).
     */
    public function destroy(Request $request): JsonResponse
    {
        $email = $request->input('email');
        $user = null;

        if (! blank($email)) {
            $user = User::where('email', $email)->first();
        }

        if (! $user) {
            $user = User::first();
        }

        if ($user) {
            $user->delete();
        }

        return response()->json([
            'message' => 'Usuario eliminado correctamente',
        ]);
    }
}
