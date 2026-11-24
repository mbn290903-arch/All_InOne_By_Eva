<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdmin extends Command
{
    protected $signature = 'app:create-admin';

    protected $description = 'Créer un compte administrateur';

    public function handle(): int
    {
        $name = getenv('ADMIN_NAME');
        $email = getenv('ADMIN_EMAIL');
        $password = getenv('ADMIN_PASSWORD');

        if (!$name || !$email || !$password) {
            $this->error('Variables administrateur manquantes.');
            return self::FAILURE;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Adresse e-mail invalide.');
            return self::FAILURE;
        }

        if (User::where('email', $email)->exists()) {
            $this->info('Ce compte administrateur existe déjà.');
            return self::SUCCESS;
        }

        $role = Role::where('name', 'admin')->first();

        if (!$role) {
            $this->error('Le rôle admin est introuvable.');
            return self::FAILURE;
        }

        User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role_id' => $role->id,
            'is_active' => true,
        ]);

        $this->info('Administrateur créé avec succès.');

        return self::SUCCESS;
    }
}