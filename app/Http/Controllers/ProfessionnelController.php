<?php

namespace App\Http\Controllers;

use App\Models\RendezVous;
use App\Services\StockService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProfessionnelController extends Controller
{
    public const STATUTS = [
        'en_attente'    => ['label' => 'En attente',    'classes' => 'bg-gray-100 text-gray-600'],
        'confirme'      => ['label' => 'Confirmé',      'classes' => 'bg-blue-50 text-blue-700'],
        'client_arrive' => ['label' => 'Client arrivé', 'classes' => 'bg-violet-50 text-violet-700'],
        'en_cours'      => ['label' => 'En cours',      'classes' => 'bg-[#f6e9ed] text-wine'],
        'termine'       => ['label' => 'Terminé',       'classes' => 'bg-emerald-50 text-emerald-700'],
    ];

    /** Changements de statut autorisés pour un professionnel. */
    public const TRANSITIONS = [
        'confirme'      => ['en_cours'],
        'client_arrive' => ['en_cours'],
        'en_cours'      => ['termine'],
    ];

    public const ACTIONS = ['en_cours' => 'Démarrer', 'termine' => 'Terminer'];

    /** Id de la fiche employé liée au compte connecté (null si non reliée). */
    private function employeId(): ?int
    {
        $id = DB::table('employes')->where('user_id', auth()->id())->value('id');

        return $id ? (int) $id : null;
    }

    /** Un professionnel ne touche qu'à SES rendez-vous. */
    private function verifierProprietaire(RendezVous $rendezVous): int
    {
        $employeId = $this->employeId();
        abort_unless($employeId && (int) $rendezVous->employe_id === $employeId, 403);

        return $employeId;
    }

    private function base(int $employeId)
    {
        return DB::table('rendez_vous as r')
            ->join('clients as c', 'c.id', '=', 'r.client_id')
            ->where('r.employe_id', $employeId)
            ->where('r.statut', '!=', 'annule')
            ->select(
                'r.id',
                'r.debut',
                'r.fin',
                'r.statut',
                'r.zone',
                'r.commentaire',
                'c.prenom',
                'c.nom'
            );
    }

    private function enrichir($rdvs)
    {
        $ids = $rdvs->pluck('id')->all();
        $services = $ids
            ? DB::table('rendez_vous_service')->whereIn('rendez_vous_id', $ids)
            ->get(['rendez_vous_id', 'libelle', 'duree_minutes'])->groupBy('rendez_vous_id')
            : collect();

        return $rdvs->map(function ($r) use ($services) {
            $r->debut = Carbon::parse($r->debut);
            $r->fin = Carbon::parse($r->fin);
            $r->services = $services->get($r->id, collect());

            return $r;
        });
    }

    public function dashboard()
    {
        $id = $this->employeId();
        if (! $id) {
            return view('professionnel.dashboard', ['employeLie' => false]);
        }

        $rdvsJour = $this->enrichir(
            $this->base($id)->whereBetween('r.debut', [now()->startOfDay(), now()->endOfDay()])
                ->orderBy('r.debut')->get()
        );

        return view('professionnel.dashboard', [
            'employeLie' => true,
            'rdvsJour'   => $rdvsJour,
            'prochain'   => $rdvsJour->first(fn($r) => $r->statut !== 'termine' && $r->fin->isFuture()),
            'stats'      => [
                'aujourdhui' => $rdvsJour->count(),
                'a_faire'    => $rdvsJour->where('statut', '!=', 'termine')->count(),
                'termines'   => $rdvsJour->where('statut', 'termine')->count(),
                'semaine'    => $this->base($id)->whereBetween('r.debut', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            ],
        ]);
    }

    public function planning(Request $request)
    {
        $id = $this->employeId();
        if (! $id) {
            return view('professionnel.planning', ['employeLie' => false]);
        }

        try {
            $debut = Carbon::parse($request->input('semaine', 'today'))->startOfWeek();
        } catch (\Throwable $e) {
            $debut = now()->startOfWeek();
        }
        $fin = $debut->copy()->endOfWeek();

        $parJour = $this->enrichir(
            $this->base($id)->whereBetween('r.debut', [$debut, $fin])->orderBy('r.debut')->get()
        )->groupBy(fn($r) => $r->debut->toDateString());

        return view('professionnel.planning', [
            'employeLie' => true,
            'debut'      => $debut,
            'fin'        => $fin,
            'parJour'    => $parJour,
        ]);
    }

    public function show(RendezVous $rendezVous)
    {
        $id = $this->verifierProprietaire($rendezVous);

        $rdv = $this->enrichir(
            $this->base($id)
                ->where('r.id', $rendezVous->id)->get()
        )->first();
        abort_unless($rdv, 404);

        return view('professionnel.show', compact('rdv'));
    }

    public function statut(Request $request, RendezVous $rendezVous)
    {
        $this->verifierProprietaire($rendezVous);

        $nouveau = $request->validate(['statut' => ['required', 'in:en_cours,termine']])['statut'];
        $ancien = $rendezVous->getRawOriginal('statut');

        if (! in_array($nouveau, self::TRANSITIONS[$ancien] ?? [], true)) {
            return back()->withErrors(['statut' => 'Ce changement de statut n’est pas possible depuis « ' . (self::STATUTS[$ancien]['label'] ?? $ancien) . ' ».']);
        }

        DB::transaction(function () use ($rendezVous, $ancien, $nouveau, $request) {
            DB::table('rendez_vous')->where('id', $rendezVous->id)
                ->update(['statut' => $nouveau, 'updated_at' => now()]);

            DB::table('historique_actions')->insert([
                'user_id'           => auth()->id(),
                'action'            => 'rendez_vous.statut',
                'auditable_type'    => RendezVous::class,
                'auditable_id'      => $rendezVous->id,
                'anciennes_valeurs' => json_encode(['statut' => $ancien]),
                'nouvelles_valeurs' => json_encode(['statut' => $nouveau]),
                'ip'                => $request->ip(),
                'created_at'        => now(),
            ]);
        });

        // Déduction du stock (sans effet si l'option est désactivée). Ne bloque jamais le changement de statut.
        if ($nouveau === 'termine') {
            try {
                app(StockService::class)->consommerPourRendezVous($rendezVous->id, auth()->user());
            } catch (\Throwable $e) {
                Log::warning("Consommation stock impossible (rendez-vous #{$rendezVous->id}) : " . $e->getMessage());
            }
        }

        return back()->with('success', $nouveau === 'en_cours' ? 'Prestation démarrée.' : 'Prestation terminée.');
    }
}
