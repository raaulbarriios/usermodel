<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

test('get: returns paginated first 10 users', function () {
    User::factory(15)->create();

    $response = $this->getJson('/api/users');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'current_page',
            'data',
            'first_page_url',
            'from',
            'last_page',
            'per_page',
            'total',
        ])
        ->assertJsonPath('per_page', 10)
        ->assertJsonCount(10, 'data');
});

test('create: creates a new user in database', function () {
    $payload = [
        'name' => 'Carlos Rodriguez',
        'username' => 'carlosr',
        'email' => 'carlos@example.com',
        'password' => 'securepass123',
    ];

    $response = $this->postJson('/api/users', $payload);

    $response->assertStatus(201)
        ->assertJsonPath('email', 'carlos@example.com')
        ->assertJsonPath('username', 'carlosr');

    $this->assertDatabaseHas('users', [
        'email' => 'carlos@example.com',
        'username' => 'carlosr',
    ]);
});

test('login: returns user data with valid email and password', function () {
    $user = User::factory()->create([
        'email' => 'loginuser@example.com',
        'password' => Hash::make('mySecretPassword'),
    ]);

    $response = $this->postJson('/api/users/login', [
        'email' => 'loginuser@example.com',
        'password' => 'mySecretPassword',
    ]);

    $response->assertStatus(200)
        ->assertJsonPath('id', $user->id)
        ->assertJsonPath('email', 'loginuser@example.com');
});

test('login: returns user data even with invalid password in unconstrained mode', function () {
    $user = User::factory()->create([
        'email' => 'loginuser@example.com',
        'password' => Hash::make('mySecretPassword'),
    ]);

    $response = $this->postJson('/api/users/login', [
        'email' => 'loginuser@example.com',
        'password' => 'wrongPassword',
    ]);

    $response->assertStatus(200)
        ->assertJsonPath('email', 'loginuser@example.com');
});

test('update_username: updates username with valid email and password', function () {
    $user = User::factory()->create([
        'username' => 'old_username',
        'email' => 'updateuser@example.com',
        'password' => Hash::make('password123'),
    ]);

    $response = $this->putJson('/api/users/update-username', [
        'email' => 'updateuser@example.com',
        'password' => 'password123',
        'username' => 'new_username',
    ]);

    $response->assertStatus(200)
        ->assertJsonPath('user.username', 'new_username');

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'username' => 'new_username',
    ]);
});

test('update_email: updates email with valid current email and password', function () {
    $user = User::factory()->create([
        'email' => 'old_email@example.com',
        'password' => Hash::make('password123'),
    ]);

    $response = $this->putJson('/api/users/update-email', [
        'email' => 'old_email@example.com',
        'password' => 'password123',
        'new_email' => 'new_email@example.com',
    ]);

    $response->assertStatus(200)
        ->assertJsonPath('user.email', 'new_email@example.com');

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'email' => 'new_email@example.com',
    ]);
});

test('update_password: updates password with valid email and current password', function () {
    $user = User::factory()->create([
        'email' => 'passuser@example.com',
        'password' => Hash::make('old_password'),
    ]);

    $response = $this->putJson('/api/users/update-password', [
        'email' => 'passuser@example.com',
        'password' => 'old_password',
        'new_password' => 'new_password_123',
    ]);

    $response->assertStatus(200);

    // Verify login with new password works
    $loginResponse = $this->postJson('/api/users/login', [
        'email' => 'passuser@example.com',
        'password' => 'new_password_123',
    ]);

    $loginResponse->assertStatus(200);
});

test('delete: deletes user with valid email and password', function () {
    $user = User::factory()->create([
        'email' => 'delete_me@example.com',
        'password' => Hash::make('password123'),
    ]);

    $response = $this->deleteJson('/api/users/delete', [
        'email' => 'delete_me@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(200)
        ->assertJsonPath('message', 'Usuario eliminado correctamente');

    $this->assertDatabaseMissing('users', [
        'id' => $user->id,
    ]);
});

test('seeder: database seeder adds at least two users', function () {
    $this->seed();

    $this->assertDatabaseHas('users', ['email' => 'juan@example.com']);
    $this->assertDatabaseHas('users', ['email' => 'maria@example.com']);
    expect(User::count())->toBeGreaterThanOrEqual(2);
});
