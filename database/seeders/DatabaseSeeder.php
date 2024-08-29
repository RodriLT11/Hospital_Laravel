<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Crear usuario Admin si no existe
        if (!User::where('email', 'admin@example.com')->exists()) {
            User::create([
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'age' => 30,
            ]);
        }

        // Crear usuario Doctor si no existe
        if (!User::where('email', 'doctor@example.com')->exists()) {
            User::create([
                'name' => 'Doctor John',
                'email' => 'doctor@example.com',
                'password' => bcrypt('password'),
                'role' => 'doctor',
                'age' => 40,
            ]);
        }

        // Crear usuario Patient si no existe
        if (!User::where('email', 'patient@example.com')->exists()) {
            User::create([
                'name' => 'Patient Jane',
                'email' => 'patient@example.com',
                'password' => bcrypt('password'),
                'role' => 'patient',
                'age' => 25,
            ]);
        }

        // Crear nuevos usuarios con la misma contraseña
        $users = [
            ['name' => 'Doctor One', 'email' => 'doctor1@example.com', 'role' => 'doctor'],
            ['name' => 'Doctor Two', 'email' => 'doctor2@example.com', 'role' => 'doctor'],
            ['name' => 'Doctor Three', 'email' => 'doctor3@example.com', 'role' => 'doctor'],
            ['name' => 'Secretary', 'email' => 'secretaria@example.com', 'role' => 'secretary'],
            ['name' => 'Patient One', 'email' => 'paciente1@example.com', 'role' => 'patient'],
            ['name' => 'Patient Two', 'email' => 'paciente2@example.com', 'role' => 'patient'],
            ['name' => 'Patient Three', 'email' => 'paciente3@example.com', 'role' => 'patient'],
        ];

        foreach ($users as $user) {
            if (!User::where('email', $user['email'])->exists()) {
                User::create([
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'password' => bcrypt('password'),
                    'role' => $user['role'],
                    'age' => rand(20, 60), // Puedes ajustar o eliminar esta línea si no necesitas edad aleatoria
                ]);
            }
        }
    }
}
