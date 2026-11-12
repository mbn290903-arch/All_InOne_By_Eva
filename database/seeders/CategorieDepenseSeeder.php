<?php

namespace Database\Seeders;

use App\Models\CategorieDepense;
use Illuminate\Database\Seeder;

class CategorieDepenseSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            'Achat de produits', 'Loyer', 'Électricité', 'Eau',
            'Salaires et commissions', 'Entretien du matériel', 'Autres dépenses',
        ] as $nom) {
            CategorieDepense::firstOrCreate(['nom' => $nom]);
        }
    }
}