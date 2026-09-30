<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crear al menos dos usuarios con credenciales conocidas
        User::updateOrCreate(
            ['email' => 'juan@example.com'],
            [
                'name' => 'Juan Pérez',
                'username' => 'juanperez',
                'password' => Hash::make('password123'),
            ]
        );

        User::updateOrCreate(
            ['email' => 'maria@example.com'],
            [
                'name' => 'María López',
                'username' => 'marialopez',
                'password' => Hash::make('secret123'),
            ]
        );

        // Crear usuarios adicionales para verificar la paginación de 10
        User::factory(10)->create();
    }
}
