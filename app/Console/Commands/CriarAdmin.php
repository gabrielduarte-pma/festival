<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CriarAdmin extends Command
{
    protected $signature = 'festival:admin {email} {senha?} {--nome=Administrador}';

    protected $description = 'Cria (ou atualiza a senha de) um administrador do festival';

    public function handle(): int
    {
        $senha = $this->argument('senha') ?: $this->secret('Senha (mínimo 8 caracteres)');

        if (! $senha || strlen($senha) < 8) {
            $this->error('A senha precisa ter pelo menos 8 caracteres.');

            return self::FAILURE;
        }

        User::updateOrCreate(
            ['email' => $this->argument('email')],
            ['name' => $this->option('nome'), 'password' => $senha, 'role' => 'admin', 'ativo' => true, 'email_verified_at' => now()]
        );

        $this->info('Administrador pronto: '.$this->argument('email'));

        return self::SUCCESS;
    }
}
