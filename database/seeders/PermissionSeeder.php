<?php
namespace Database\Seeders;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        Permission::firstOrCreate(['name' => 'paiements.annuler'], ['label' => 'Annuler ou corriger un paiement']);

        // L'admin reçoit toutes les permissions
        Role::where('name', 'admin')->first()
            ?->permissions()->syncWithoutDetaching(Permission::pluck('id'));
    }
}