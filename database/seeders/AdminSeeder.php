<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /** Cria o administrador inicial a partir de ADMIN_EMAIL / ADMIN_PASSWORD / ADMIN_NAME no .env. */
    public function run(): void
    {
        $email = env('ADMIN_EMAIL');
        $senha = env('ADMIN_PASSWORD');

        if (! $email || ! $senha) {
            $this->command?->warn('ADMIN_EMAIL/ADMIN_PASSWORD não definidos: nenhum admin criado. Use: php artisan festival:admin email senha');

            return;
        }

        User::updateOrCreate(
            ['email' => $email],
            ['name' => env('ADMIN_NAME', 'Administrador'), 'password' => $senha, 'role' => 'admin', 'ativo' => true, 'email_verified_at' => now()]
        );
        $this->command?->info("Admin pronto: {$email}");
    }
}
