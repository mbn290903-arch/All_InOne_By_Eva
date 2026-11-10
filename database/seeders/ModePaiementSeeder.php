<?php

namespace Database\Seeders;

use App\Models\ModePaiement;
use Illuminate\Database\Seeder;

class ModePaiementSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Espèces', 'Wave', 'Orange Money', 'Carte bancaire'] as $libelle) {
            ModePaiement::firstOrCreate(['libelle' => $libelle]);
        }
    }
}