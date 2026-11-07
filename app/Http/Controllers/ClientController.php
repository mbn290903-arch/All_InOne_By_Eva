<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ClientController extends Controller
{
    /* ------------------------------------------------------------------
     |  LISTE
     * ----------------------------------------------------------------*/
    public function index(Request $request)
    {
        $q   = trim((string) $request->q);
        $tab = $request->get('tab', 'tous');
        $tri = $request->get('tri', 'recent');

        $clients = $this->filtered($request)->paginate(20)->withQueryString();

        $actifs = Client::whereNull('archived_at');
        $stats = [
            'tous'         => (clone $actifs)->count(),
            'actifs'       => (clone $actifs)->where('statut', 'actif')->count(),
            'inactifs'     => (clone $actifs)->where('statut', 'inactif')->count(),
            'anniversaire' => $this->anniversaire(clone $actifs)->count(),
            'archives'     => Client::whereNotNull('archived_at')->count(),
        ];

        $doublons = Client::whereNull('archived_at')
            ->select('telephone')->groupBy('telephone')
            ->havingRaw('count(*) > 1')->pluck('telephone');

        return view('clients.index', compact('clients', 'stats', 'doublons', 'q', 'tab', 'tri'));
    }

    public function create()
    {
        $nextCode = 'CL-' . str_pad((Client::max('id') ?? 0) + 1, 6, '0', STR_PAD_LEFT);

        return view('clients.create', compact('nextCode'));
    }

    public function show(Client $client)
    {
        return view('clients.show', compact('client'));
    }

    /* ------------------------------------------------------------------
     |  CRÉATION
     * ----------------------------------------------------------------*/
    public function store(Request $request)
    {
        $data = $this->validated($request);

        // BUG 1 corrigé : « Créer quand même » (force=1) est enfin lu.
        if (! $request->boolean('force') && $existant = $this->doublon($data['telephone'])) {
            return back()->withInput()->with('doublon', $this->infosDoublon($existant));
        }

        $consent = $request->boolean('consent_promotions');

        $client = Client::create($data + [
            'date_inscription'   => now()->toDateString(),
            'consent_promotions' => $consent,
            'consent_at'         => $consent ? now() : null,
            'consent_par'        => $consent ? Auth::id() : null,   // BUG 4 corrigé
        ]);

        return redirect()->route('clients.index')->with('status', [
            'Client enregistré',
            "{$client->prenom} {$client->nom} ({$this->code($client)}) a été ajouté(e).",
        ]);
    }

    /* ------------------------------------------------------------------
     |  MODIFICATION
     * ----------------------------------------------------------------*/
    public function edit(Client $client)
    {
        Gate::authorize('update', $client);

        $nextCode = $this->code($client);

        return view('clients.edit', compact('client', 'nextCode'));
    }

    public function update(Request $request, Client $client)
    {
        Gate::authorize('update', $client);

        $data = $this->validated($request);

        if (! $request->boolean('force') && $existant = $this->doublon($data['telephone'],(int) $client->getKey())) {
            return back()->withInput()->with('doublon', $this->infosDoublon($existant));
        }

        // La date et l'auteur du consentement ne changent que si la valeur change.
        $consent = $request->boolean('consent_promotions');
        if ($consent !== (bool) $client->consent_promotions) {
            $data += [
                'consent_promotions' => $consent,
                'consent_at'         => now(),
                'consent_par'        => Auth::id(),
            ];
        }

        $client->update($data);

        return redirect()->route('clients.show', $client)->with('status', [
            'Fiche mise à jour',
            "{$client->prenom} {$client->nom} ({$this->code($client)}).",
        ]);
    }

    /* ------------------------------------------------------------------
     |  ARCHIVAGE (jamais de suppression)
     * ----------------------------------------------------------------*/
    public function archive(Client $client)
    {
        Gate::authorize('archive', $client);

        $client->update([
            'statut'      => 'inactif',
            'archived_at' => now(),
            'archived_by' => Auth::id(),
        ]);

        return redirect()->route('clients.index')->with('status', [
            'Client archivé',
            "{$client->prenom} {$client->nom} est archivé(e). L'historique est conservé.",
        ]);
    }

    public function restore(Client $client)
    {
        Gate::authorize('archive', $client);

        $client->update(['archived_at' => null, 'archived_by' => null]);

        return redirect()->route('clients.show', $client)->with('status', [
            'Client restauré',
            "{$client->prenom} {$client->nom} est de nouveau dans la liste (statut inactif).",
        ]);
    }

    /* ------------------------------------------------------------------
     |  EXPORT CSV
     * ----------------------------------------------------------------*/
    public function export(Request $request): StreamedResponse
    {
        Gate::authorize('export', Client::class);

        $query    = $this->filtered($request)->reorder();
        $filename = 'clients-' . now()->format('Ymd-His') . '.csv';

        Log::info('Export clients CSV', [
            'user_id' => Auth::id(),
            'filtres' => $request->only(['q', 'tab', 'tri']),
        ]);

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM UTF-8 pour Excel

            $put = fn(array $row) => fputcsv($out, array_map([$this, 'csvSafe'], $row), ';', '"', '');

            $put(['Identifiant', 'Prénom', 'Nom', 'Public', 'Téléphone', 'WhatsApp', 'E-mail',
                  'Date de naissance', 'Date inscription', 'Canal préféré', 'Consentement promo',
                  'Date consentement', 'Statut', 'Archivé le']);

            $query->chunkById(500, function ($rows) use ($put) {
                foreach ($rows as $c) {
                    $put([
                        $this->code($c),
                        $c->prenom,
                        $c->nom,
                        $c->public,
                        $c->telephone ? '221' . $c->telephone : '',
                        $c->whatsapp ? '221' . $c->whatsapp : '',
                        $c->email,
                        $c->date_naissance?->format('d/m/Y'),
                        $c->date_inscription?->format('d/m/Y'),
                        $c->canal_prefere,
                        $c->consent_promotions ? 'Oui' : 'Non',
                        $c->consent_at?->format('d/m/Y'),
                        $c->statut,
                        $c->archived_at?->format('d/m/Y'),
                    ]);
                }
            });

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /* ==================================================================
     |  OUTILS PRIVÉS
     * ================================================================*/

    /** Requête filtrée, partagée par la liste et l'export. */
    private function filtered(Request $request): Builder
    {
        $q   = trim((string) $request->q);
        $tab = $request->get('tab', 'tous');
        $tri = $request->get('tri', 'recent');

        $query = Client::query();

        $tab === 'archives'
            ? $query->whereNotNull('archived_at')
            : $query->whereNull('archived_at');

        if ($q !== '') {
            $digits = preg_replace('/\D/', '', $q);
            $query->where(function ($w) use ($q, $digits) {
                $w->where('prenom', 'like', "%{$q}%")->orWhere('nom', 'like', "%{$q}%");

                if ($digits !== '') {
                    $w->orWhere('telephone', 'like', "%{$digits}%")
                      ->orWhere('whatsapp', 'like', "%{$digits}%");
                }
                // Recherche par identifiant : CL-000012 ou CL000012
                if (preg_match('/^CL-?0*(\d+)$/i', $q, $m)) {
                    $w->orWhere('id', (int) $m[1]);
                }
            });
        }

        if ($tab === 'actifs')       { $query->where('statut', 'actif'); }
        if ($tab === 'inactifs')     { $query->where('statut', 'inactif'); }
        if ($tab === 'anniversaire') { $this->anniversaire($query); }

        $tri === 'nom'
            ? $query->orderBy('nom')->orderBy('prenom')
            : $query->orderByDesc('id');

        return $query;
    }

    /**
     * Anniversaires des 30 prochains jours, en SQL (plus de ->get() sur toute la table).
     * On compare le « mois-jour » à la liste des 31 prochains jours du calendrier réel :
     * pas de décalage au 29 février (une personne née un 29/02 apparaît le 29/02 les
     * années bissextiles ; en année normale, ajoutez '02-28' si vous voulez la fêter).
     */
    private function anniversaire(Builder $query): Builder
    {
        $jours = collect(range(0, 30))
            ->map(fn($i) => now()->startOfDay()->addDays($i)->format('m-d'))
            ->all();

        $expr = DB::getDriverName() === 'sqlite'
            ? "strftime('%m-%d', date_naissance)"
            : "DATE_FORMAT(date_naissance, '%m-%d')";

        return $query->whereNotNull('date_naissance')
            ->whereRaw("{$expr} in (" . implode(',', array_fill(0, count($jours), '?')) . ')', $jours);
    }

    /** Normalise puis valide. BUG 2 et 3 : noms de champs uniques + champ `public`. */
    private function validated(Request $request): array
    {
        $tel = preg_replace('/\D/', '', (string) $request->telephone);
        $wa  = $request->boolean('whatsapp_same') ? $tel : preg_replace('/\D/', '', (string) $request->whatsapp);
        $request->merge(['telephone' => $tel, 'whatsapp' => $wa]);

        return $request->validate([
            'prenom'         => 'required|string|max:80',
            'nom'            => 'required|string|max:80',
            'public'         => 'required|in:femme,homme,enfant',
            'date_naissance' => 'nullable|date|before:today',
            'telephone'      => 'required|digits:9',
            'whatsapp'       => 'required|digits:9',
            'email'          => 'nullable|email|max:120',
            'canal_prefere'  => 'required|in:whatsapp,appel,sms',
            'notes'          => 'nullable|string|max:1000',
            'statut'         => 'required|in:actif,inactif',
        ], [
            'prenom.required'        => 'Le prénom est obligatoire.',
            'nom.required'           => 'Le nom est obligatoire.',
            'public.required'        => 'Précisez le public (femme, homme ou enfant).',
            'public.in'              => 'Public invalide.',
            'telephone.required'     => 'Le téléphone est obligatoire.',
            'telephone.digits'       => 'Numéro incomplet : 9 chiffres attendus.',
            'whatsapp.required'      => 'Le numéro WhatsApp est obligatoire.',
            'whatsapp.digits'        => 'Numéro incomplet : 9 chiffres attendus.',
            'email.email'            => 'Adresse e-mail invalide.',
            'date_naissance.before'  => 'La date de naissance doit être dans le passé.',
        ]);
    }

    private function doublon(string $telephone, ?int $ignorerId = null): ?Client
    {
        return Client::where('telephone', $telephone)
            ->when($ignorerId, fn($q) => $q->where('id', '!=', $ignorerId))
            ->first();
    }

    private function infosDoublon(Client $c): array
    {
        return [
            'id' => $c->getKey(),
            'nom'  => trim($c->prenom . ' ' . $c->nom),
            'code' => $this->code($c),
        ];
    }

    private function code(Client $c): string
    {
        return 'CL-' . str_pad((string) $c->getKey(), 6, '0', STR_PAD_LEFT);
    }

    /** Neutralise l'injection de formules (=, +, -, @, tab, retour chariot) dans Excel/Sheets. */
    private function csvSafe($value): string
    {
        $value = (string) $value;

        return preg_match('/^[=+\-@\t\r]/', $value) ? "'" . $value : $value;
    }
}
