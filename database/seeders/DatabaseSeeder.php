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
        // Criar usuário admin de teste
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@exemplo.com',
            'password' => bcrypt('password'),
            'balance' => 10000.00,
        ]);

        // Criar usuário de teste comum
        User::factory()->create([
            'name' => 'Teste User',
            'email' => 'teste@exemplo.com',
            'password' => bcrypt('password'),
            'balance' => 1000.00,
        ]);

        // Criar usuário com saldo baixo
        User::factory()->create([
            'name' => 'João Silva',
            'email' => 'joao@exemplo.com',
            'password' => bcrypt('password'),
            'balance' => 50.00,
        ]);

        // Criar mais 5 usuários aleatórios
        User::factory(5)->create();
    }
}
