<?php

namespace App\Http\Controllers;

use App\Enums\StatutRendezVous;
use App\Models\RendezVous;
use Carbon\CarbonImmutable;

class DashboardController extends Controller
{
    public function admin()
    {
        $maintenant = CarbonImmutable::now();
        $debutJour = $maintenant->startOfDay();
        $finJour = $maintenant->endOfDay();

        // ── Rendez-vous du jour (hors annulés) ──
        // Rendez-vous du jour (hors annulés et absents)
$actifsDuJour = RendezVous::query()
    ->whereBetween('debut', [$debutJour, $finJour])
    ->get(['id', 'statut'])
    ->filter(fn ($r) => ! $r->statut->libereLeCreneau());

        $total = $actifsDuJour->count();

        $confirmes = $actifsDuJour->filter(fn ($r) => in_array($r->statut, [
            StatutRendezVous::Confirme,
            StatutRendezVous::ClientArrive,
            StatutRendezVous::EnCours,
            StatutRendezVous::Termine,
        ], true))->count();

        $enAttente = $actifsDuJour
            ->filter(fn ($r) => $r->statut === StatutRendezVous::EnAttente)
            ->count();

        $termines = $actifsDuJour
            ->filter(fn ($r) => $r->statut === StatutRendezVous::Termine)
            ->count();

        $progression = $total > 0 ? (int) round($termines / $total * 100) : 0;
        // ── Rendez-vous à venir sur 7 jours, comparés aux 7 jours précédents ──
        $annule = StatutRendezVous::Annule->value;

        $aVenir = RendezVous::query()
            ->whereBetween('debut', [$maintenant, $maintenant->addDays(7)])
            ->where('statut', '!=', $annule)
            ->count();

        $precedents = RendezVous::query()
            ->whereBetween('debut', [$maintenant->subDays(7), $maintenant])
            ->where('statut', '!=', $annule)
            ->count();

        $evolution = $precedents > 0
            ? (int) round(($aVenir - $precedents) / $precedents * 100)
            : null;

        // ── Prochains rendez-vous d'aujourd'hui (en cours ou à venir) ──
        $prochains = RendezVous::query()
            ->with(['client', 'employe', 'lignes'])
            ->where('fin', '>=', $maintenant)
            ->where('debut', '<=', $finJour)
            ->bloquants()
            ->orderBy('debut')
            ->limit(5)
            ->get();

        // ── Rendez-vous de demain non confirmés ──
        $demain = $maintenant->addDay();

        $nonConfirmesDemain = RendezVous::query()
            ->with('client')
            ->whereBetween('debut', [$demain->startOfDay(), $demain->endOfDay()])
            ->where('statut', StatutRendezVous::EnAttente->value)
            ->orderBy('debut')
            ->get();

        return view('admin.dashboard', compact(
            'total',
            'confirmes',
            'enAttente',
            'termines',
            'progression',
            'aVenir',
            'evolution',
            'prochains',
            'nonConfirmesDemain'
        ));
    }
}