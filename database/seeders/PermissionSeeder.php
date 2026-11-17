<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            'paiements.enregistrer' => 'Enregistrer un paiement',
            'paiements.annuler' => 'Annuler ou corriger un paiement',
        ] as $nom => $label) {
            Permission::firstOrCreate(['name' => $nom], ['label' => $label]);
        }

        $ids = Permission::pluck('id', 'name');

        // L'admin reçoit toutes les permissions
        Role::where('name', 'admin')->first()
            ?->permissions()->syncWithoutDetaching($ids->values()->all());

        // La réceptionniste peut enregistrer des paiements, mais pas les annuler
        Role::where('name', 'receptionniste')->first()
            ?->permissions()->syncWithoutDetaching([$ids['paiements.enregistrer']]);
    }
}