<?php

namespace App\Http\Controllers;

use App\Models\Employe;
use App\Models\RendezVous;
use App\Support\DispositionCalendrier;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CalendrierController extends Controller
{
    public function index(Request $request)
    {
        // Vue demandée : jour, semaine, mois ou liste
        $vue = $request->query('vue', 'semaine');

        // Date sélectionnée ou date d'aujourd'hui
        $reference = Carbon::parse($request->query('date', now()->toDateString()));

        // Déterminer la période selon la vue
        switch ($vue) {

            case 'jour':
                $debut = $reference->copy()->startOfDay();
                $finExclusive = $debut->copy()->addDay();
                break;

            case 'mois':
                $debut = $reference->copy()->startOfMonth();
                $finExclusive = $debut->copy()->addMonth();
                break;

            case 'liste':
            case 'semaine':
            default:
                $vue = $vue === 'liste' ? 'liste' : 'semaine';

                $debut = $reference->copy()->startOfWeek(Carbon::MONDAY);
                $finExclusive = $debut->copy()->addWeek();
                break;
        }

        // Récupérer les rendez-vous de la période
        $rdvs = RendezVous::query()
            ->entre($debut, $finExclusive)
            ->when(
                $request->employe_id,
                fn ($q, $id) => $q->pourEmploye($id)
            )
            ->with(['client', 'employe', 'lignes'])
            ->orderBy('debut')
            ->get();

        // Construire les jours de la période
        $nombreJours = (int) $debut->diffInDays($finExclusive);

        $jours = collect(range(0, $nombreJours - 1))->map(function ($i) use ($debut, $rdvs) {

            $date = $debut->copy()->addDays($i);

            $duJour = $rdvs
                ->filter(fn ($r) => $r->debut->isSameDay($date))
                ->values();

            return [
                'date' => $date,
                'ferme' => in_array(
                    $date->dayOfWeekIso,
                    config('salon.jours_fermes')
                ),
                'rdvs' => DispositionCalendrier::placer($duJour),
            ];
        });

        // Vue mois : cases vides avant le 1er et après le dernier jour
        if ($vue === 'mois') {

            // Lundi = 0 ... dimanche = 6
            $casesVides = $debut->dayOfWeekIso - 1;

            $jours = collect(array_fill(0, $casesVides, null))
                ->concat($jours);

            // Compléter la dernière semaine jusqu'au dimanche
            $casesFin = (7 - ($jours->count() % 7)) % 7;

            $jours = $jours->concat(
                collect(array_fill(0, $casesFin, null))
            );
        }

        return view('rendez-vous.calendrier.semaine', [
            'jours' => $jours,
            'rdvs' => $rdvs,
            'debut' => $debut,
            'fin' => $finExclusive->copy()->subDay(),
            'vue' => $vue,
            'employes' => Employe::orderBy('nom')->get(),
            'employeId' => $request->employe_id,
        ]);
    }
}