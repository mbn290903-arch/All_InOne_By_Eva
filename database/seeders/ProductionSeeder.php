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
        if (! Role::query()->exists()) {
            $this->call([
                RoleSeeder::class,
                SalonSeeder::class,
                ModePaiementSeeder::class,
                PermissionSeeder::class,
                CategorieDepenseSeeder::class,
            ]);
        }

        $this->creerUtilisateur('ADMIN', 'admin', 'Administratrice');
        $this->creerUtilisateur('RECEPTION', 'receptionniste', 'Réceptionniste');
    }

    private function creerUtilisateur(string $prefixe, string $role, string $nomParDefaut): void
    {
        $email = env("{$prefixe}_EMAIL");
        $motDePasse = env("{$prefixe}_PASSWORD");

        if (! $email || ! $motDePasse) {
            return;
        }

        User::firstOrCreate(['email' => $email], [
            'name' => env("{$prefixe}_NAME", $nomParDefaut),
            'password' => Hash::make($motDePasse),
            'role_id' => Role::query()->where('name', $role)->value('id'),
            'is_active' => true,
        ]);
    }
}