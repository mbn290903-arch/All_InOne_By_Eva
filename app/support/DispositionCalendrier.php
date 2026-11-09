<?php

namespace App\Support;

use Illuminate\Support\Collection;

class DispositionCalendrier
{
    public static function placer(Collection $rdvs): Collection
    {
        // 1. Former les groupes de RDV qui se touchent
        $groupes   = [];
        $courant   = [];
        $finGroupe = null;

        foreach ($rdvs->sortBy('debut') as $rdv) {
            if ($finGroupe && $rdv->debut >= $finGroupe) {
                $groupes[] = $courant;
                $courant   = [];
                $finGroupe = null;
            }
            $courant[]  = $rdv;
            $finGroupe  = $finGroupe ? max($finGroupe, $rdv->fin) : $rdv->fin;
        }
        if ($courant) {
            $groupes[] = $courant;
        }

        // 2. Dans chaque groupe, donner une colonne à chaque RDV
        foreach ($groupes as $groupe) {
            $colonnes = []; // heure de fin du dernier RDV posé dans chaque colonne

            foreach ($groupe as $rdv) {
                $i = 0;
                while (isset($colonnes[$i]) && $colonnes[$i] > $rdv->debut) {
                    $i++;
                }
                $colonnes[$i] = $rdv->fin;
                $rdv->colonne = $i;
            }

            foreach ($groupe as $rdv) {
                $rdv->nb_colonnes = count($colonnes);
            }
        }
        return $rdvs;
    }
}