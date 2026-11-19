<?php
namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/** Données minimales pour la production (sans données de démonstration). */
class ProductionSeeder extends Seeder
{
    public function run(): void
    {
        // Déjà initialisé : on ne refait rien (sûr à chaque redémarrage).
        if (User::query()->exists()) {
            return;
        }

        $email = env('ADMIN_EMAIL');
        $motDePasse = env('ADMIN_PASSWORD');

        if (! $email || ! $motDePasse) {
            $this->command->error('ADMIN_EMAIL et ADMIN_PASSWORD sont obligatoires.');
            return;
        }

        $this->call([
            RoleSeeder::class,
            SalonSeeder::class,
            ModePaiementSeeder::class,
            PermissionSeeder::class,
            CategorieDepenseSeeder::class,
        ]);

        User::create([
            'name' => env('ADMIN_NAME', 'Administratrice'),
            'email' => $email,
            'password' => Hash::make($motDePasse),
            'role_id' => Role::query()->where('name', 'admin')->value('id'),
            'is_active' => true,
        ]);
    }
}