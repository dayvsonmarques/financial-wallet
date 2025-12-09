<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Usuários fixos: idempotente usando updateOrCreate por email
        User::updateOrCreate(
            ['email' => 'admin@exemplo.com'],
            [
                'name' => 'Admin User',
                'email_verified_at' => now(),
                'password' => bcrypt('password'),
                'balance' => 10000.00,
            ]
        );

        User::updateOrCreate(
            ['email' => 'teste@exemplo.com'],
            [
                'name' => 'Teste User',
                'email_verified_at' => now(),
                'password' => bcrypt('password'),
                'balance' => 1000.00,
            ]
        );

        User::updateOrCreate(
            ['email' => 'joao@exemplo.com'],
            [
                'name' => 'João Silva',
                'email_verified_at' => now(),
                'password' => bcrypt('password'),
                'balance' => 50.00,
            ]
        );

        // Usuário com saldo negativo para testes manuais
        User::updateOrCreate(
            ['email' => 'negativo@exemplo.com'],
            [
                'name' => 'Usuário Negativo',
                'email_verified_at' => now(),
                'password' => bcrypt('password'),
                'balance' => -100.00,
            ]
        );

        // Criar usuários aleatórios apenas se ainda houver poucos usuários
        $desiredTotal = 9; // 4 fixos + 5 aleatórios
        $current = User::count();
        if ($current < $desiredTotal) {
            $toCreate = $desiredTotal - $current;
            User::factory($toCreate)->create();
        }
    }
}
