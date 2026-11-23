<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        // S'assure que les rôles et permissions existent
        (new \Database\Seeders\RoleSeeder())->run();

        $comptes = [
            ['Réceptionniste', 'reception@exemple.com', 'receptionniste'],
            ['Professionnel',  'pro@exemple.com',       'professionnel'],
        ];

        foreach ($comptes as [$nom, $email, $role]) {
            if (User::where('email', $email)->exists()) {
                continue;
            }

            User::create([
                'name'      => $nom,
                'email'     => $email,
                'password'  => Hash::make(env('MOT_DE_PASSE_INITIAL', str()->random(24))),
                'role_id'   => Role::where('name', $role)->value('id'),
                'is_active' => true,
            ]);
        }
    }

    public function down(): void {}
};