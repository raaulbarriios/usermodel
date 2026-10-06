<?php

use App\Models\Token;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

test('get: returns first 10 users with unified response structure', function () {
    User::factory(15)->create();

    $response = $this->getJson('/get');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
        ])
        ->assertJsonStructure([
            'success',
            'message',
            'data',
        ]);

    expect(count($response->json('data')))->toBe(10);
});

test('create: creates a new user with hashed password', function () {
    $payload = [
        'name' => 'Carlos Rodríguez',
        'username' => 'carlosr',
        'email' => 'carlos@example.com',
        'password' => 'password123',
    ];

    $response = $this->postJson('/create', $payload);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'message' => 'Usuario creado correctamente.',
        ])
        ->assertJsonPath('data.email', 'carlos@example.com');

    $user = User::where('email', 'carlos@example.com')->first();
    expect($user)->not->toBeNull();
    expect(Hash::check('password123', $user->password))->toBeTrue();
});

test('login: authenticates user and generates session token', function () {
    $user = User::factory()->create([
        'email' => 'juan@example.com',
        'password' => Hash::make('password123'),
    ]);

    $response = $this->postJson('/login', [
        'email' => 'juan@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Sesión iniciada correctamente.',
        ]);

    $token = $response->json('data');
    expect($token)->toBeString();
    expect($token)->not->toBeEmpty();

    $this->assertDatabaseHas('tokens', [
        'user_id' => $user->id,
        'token' => $token,
    ]);
});

test('login: fails with incorrect password', function () {
    User::factory()->create([
        'email' => 'juan@example.com',
        'password' => Hash::make('password123'),
    ]);

    $response = $this->postJson('/login', [
        'email' => 'juan@example.com',
        'password' => 'wrongpassword456',
    ]);

    $response->assertStatus(401)
        ->assertJson([
            'success' => false,
            'message' => 'Credenciales incorrectas.',
            'data' => null,
        ]);
});

test('updateName: updates user name passing valid token and new name', function () {
    $user = User::factory()->create([
        'name' => 'Nombre Antiguo',
    ]);

    $tokenRecord = Token::create([
        'user_id' => $user->id,
        'token' => 'test_secret_token_123',
    ]);

    $response = $this->postJson('/update-name', [
        'token' => 'test_secret_token_123',
        'name' => 'Nombre Nuevo',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Nombre actualizado correctamente.',
        ])
        ->assertJsonPath('data.name', 'Nombre Nuevo');

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'Nombre Nuevo',
    ]);
});

test('updateName: fails when token is invalid', function () {
    $response = $this->postJson('/update-name', [
        'token' => 'invalid_token',
        'name' => 'Nuevo Nombre',
    ]);

    $response->assertStatus(401)
        ->assertJson([
            'success' => false,
            'message' => 'Token inválido o expirado.',
            'data' => null,
        ]);
});
