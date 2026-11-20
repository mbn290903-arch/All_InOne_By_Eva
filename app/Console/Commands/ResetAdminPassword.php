<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ResetAdminPassword extends Command
{
    protected $signature = 'admin:reset-password';

    protected $description = 'Reset administrator password';
    public function handle(): int
    {
        $email = config('admin.email');
        $password = config('admin.password');

        if (empty($password)) {
            $this->error('La variable ADMIN_PASSWORD est absente.');
            return self::FAILURE;
        }

        $admin = User::where('email', $email)->first();

        if (! $admin) {
            $this->error("Administrateur introuvable : {$email}");
            return self::FAILURE;
        }

        $admin->password = Hash::make($password);
        $admin->save();

        $this->info('Mot de passe administrateur mis à jour avec succès.');

        return self::SUCCESS;
    }
}
