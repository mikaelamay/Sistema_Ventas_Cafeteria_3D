<?php

namespace Database\Seeders;

use App\Enums\Rol;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Seed the application's administrator and sample customer accounts.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@cafeteria.test'],
            [
                'name' => 'Administrador Cafetería',
                'password' => 'password',
                'role' => Rol::Admin->value,
                'email_verified_at' => now(),
            ],
        );

        User::updateOrCreate(
            ['email' => 'cliente@cafeteria.test'],
            [
                'name' => 'Cliente de Ejemplo',
                'password' => 'password',
                'role' => Rol::Cliente->value,
                'email_verified_at' => now(),
            ],
        );
    }
}
