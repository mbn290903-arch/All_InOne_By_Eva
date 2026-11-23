<?php

use App\Models\Employe;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $user = User::where('email', 'pro@exemple.com')->first(); // MODIFIER : même e-mail que la migration des comptes

        if (! $user || Employe::where('user_id', $user->id)->exists()) {
            return;
        }

        // Cas 1 : la fiche existe déjà
        $employe = Employe::where('prenom', 'Fatou') // MODIFIER : prénom de l'employé existant
            ->where('nom', 'Sarr')                    // MODIFIER : nom de l'employé existant
            ->whereNull('user_id')
            ->first();

        if ($employe) {
            $employe->update(['user_id' => $user->id]);
            return;
        }

        // Cas 2 : la fiche n'existe pas, on la crée
        $employe = Employe::create([
            'user_id'         => $user->id,
            'prenom'          => 'Prénom',
            'nom'             => 'Nom',
            'telephone'       => '770000000',
            'fonction'        => 'Coiffeuse',
            'specialites'     => '',
            'taux_commission' => 10,
            'is_active'       => true,
        ]);

        foreach (range(1, 6) as $jour) {
            $employe->disponibilites()->create([
                'jour_semaine' => $jour,
                'heure_debut'  => '08:00',
                'heure_fin'    => '22:00',
            ]);
        }
    }

    public function down(): void {}
};