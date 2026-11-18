<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ModeleMessage;
use App\Services\WhatsAppService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ClientController extends Controller
{
    /* ------------------------------------------------------------------
     | LISTE
     * ----------------------------------------------------------------*/

    public function index(Request $request)
    {
        Gate::authorize('viewAny', Client::class);

        $q   = trim((string) $request->q);
        $tab = $request->get('tab', 'tous');
        $tri = $request->get('tri', 'recent');

        $clients = $this->filtered($request)
            ->paginate(20)
            ->withQueryString();

        $actifs = Client::whereNull('archived_at');

        $stats = [
            'tous'         => (clone $actifs)->count(),
            'actifs'       => (clone $actifs)->where('statut', 'actif')->count(),
            'inactifs'     => (clone $actifs)->where('statut', 'inactif')->count(),
            'anniversaire' => $this->anniversaire(clone $actifs)->count(),
            'archives'     => Client::whereNotNull('archived_at')->count(),
        ];

        $doublons = Client::whereNull('archived_at')
            ->select('telephone')
            ->groupBy('telephone')
            ->havingRaw('count(*) > 1')
            ->pluck('telephone');

        return view('clients.index', compact(
            'clients',
            'stats',
            'doublons',
            'q',
            'tab',
            'tri'
        ));
    }

    /* ------------------------------------------------------------------
     | CRÉATION : FORMULAIRE
     * ----------------------------------------------------------------*/

    public function create()
    {
        Gate::authorize('create', Client::class);

        $nextCode = 'CL-' . str_pad(
            (Client::max('id') ?? 0) + 1,
            6,
            '0',
            STR_PAD_LEFT
        );

        return view('clients.create', compact('nextCode'));
    }

    /* ------------------------------------------------------------------
     | FICHE CLIENT
     * ----------------------------------------------------------------*/

    public function show(Client $client)
    {
        Gate::authorize('view', $client);

        $user = auth()->user();

        // WhatsApp uniquement si la cliente a choisi ce canal
        // et si l'utilisateur possède la permission nécessaire.
        $peutWa = $client->canal_prefere === 'whatsapp'
            && ($user->isAdmin() || $user->hasPermission('whatsapp.envoyer'));

        // Les messages ne sont préparés que si WhatsApp est autorisé.
        $wa = $peutWa
            ? $this->messagesWhatsApp($client)
            : [
                'messages' => [],
                'rdv_trouve' => false,
            ];

        return view('clients.show', [
            'client'     => $client,
            'messagesWa' => $wa['messages'],
            'rdvTrouve'  => $wa['rdv_trouve'],

            'waNumero' => $client->canal_prefere === 'whatsapp'
                ? WhatsAppService::normaliser(
                    $client->whatsapp ?: $client->telephone
                )
                : null,

            'peutWa' => $peutWa,
        ]);
    }

    /* ------------------------------------------------------------------
     | CRÉATION : ENREGISTREMENT
     * ----------------------------------------------------------------*/

    public function store(Request $request)
    {
        Gate::authorize('create', Client::class);

        $data = $this->validated($request);

        // Détection des doublons téléphoniques.
        if (
            ! $request->boolean('force')
            && $existant = $this->doublon($data['telephone'])
        ) {
            return back()
                ->withInput()
                ->with('doublon', $this->infosDoublon($existant));
        }

        $consent = $request->boolean('consent_promotions');

        $client = Client::create($data + [
            'date_inscription'   => now()->toDateString(),
            'consent_promotions' => $consent,
            'consent_at'         => $consent ? now() : null,
        ]);

        return redirect()
            ->route('clients.index')
            ->with('status', [
                'Client enregistré',
                "{$client->prenom} {$client->nom} ({$this->code($client)}) a été ajouté(e).",
            ]);
    }

    /* ------------------------------------------------------------------
     | MODIFICATION : FORMULAIRE
     * ----------------------------------------------------------------*/

    public function edit(Client $client)
    {
        Gate::authorize('update', $client);

        $nextCode = $this->code($client);

        return view('clients.edit', compact('client', 'nextCode'));
    }

    /* ------------------------------------------------------------------
     | MODIFICATION : ENREGISTREMENT
     * ----------------------------------------------------------------*/

    public function update(Request $request, Client $client)
    {
        Gate::authorize('update', $client);

        $data = $this->validated($request);

        if (
            ! $request->boolean('force')
            && $existant = $this->doublon($data['telephone'], $client->id)
        ) {
            return back()
                ->withInput()
                ->with('doublon', $this->infosDoublon($existant));
        }

        // Le consentement et son historique ne changent
        // que si la valeur a réellement été modifiée.
        $consent = $request->boolean('consent_promotions');

        if ($consent !== (bool) $client->consent_promotions) {
            $data += [
                'consent_promotions' => $consent,
                'consent_at'         => now(),
            ];
        }

        $client->update($data);

        return redirect()
            ->route('clients.show', $client)
            ->with('status', [
                'Fiche mise à jour',
                "{$client->prenom} {$client->nom} ({$this->code($client)}).",
            ]);
    }

    /* ------------------------------------------------------------------
     | ARCHIVAGE (JAMAIS DE SUPPRESSION)
     * ----------------------------------------------------------------*/

    public function archive(Client $client)
    {
        Gate::authorize('archive', $client);

        $client->update([
            'statut'      => 'inactif',
            'archived_at' => now(),
            'archived_by' => auth()->id(),
        ]);

        return redirect()
            ->route('clients.index')
            ->with('status', [
                'Client archivé',
                "{$client->prenom} {$client->nom} est archivé(e). L'historique est conservé.",
            ]);
    }

    public function restore(Client $client)
    {
        Gate::authorize('archive', $client);

        $client->update([
            'archived_at' => null,
            'archived_by' => null,
        ]);

        return redirect()
            ->route('clients.show', $client)
            ->with('status', [
                'Client restauré',
                "{$client->prenom} {$client->nom} est de nouveau dans la liste (statut inactif).",
            ]);
    }

    /* ------------------------------------------------------------------
     | EXPORT CSV
     * ----------------------------------------------------------------*/

    public function export(Request $request): StreamedResponse
    {
        Gate::authorize('export', Client::class);

        $query = $this->filtered($request)->reorder();

        $filename = 'clients-' . now()->format('Ymd-His') . '.csv';

        Log::info('Export clients CSV', [
            'user_id' => auth()->id(),
            'filtres' => $request->only(['q', 'tab', 'tri']),
        ]);

        return response()->streamDownload(function () use ($query) {

            $out = fopen('php://output', 'w');

            // BOM UTF-8 pour Excel.
            fwrite($out, "\xEF\xBB\xBF");

            $put = fn(array $row) => fputcsv(
                $out,
                array_map([$this, 'csvSafe'], $row),
                ';',
                '"',
                ''
            );

            $put([
                'Identifiant',
                'Prénom',
                'Nom',
                'Public',
                'Téléphone',
                'WhatsApp',
                'E-mail',
                'Date de naissance',
                'Date inscription',
                'Canal préféré',
                'Consentement promo',
                'Date consentement',
                'Statut',
                'Archivé le',
            ]);

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
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /* ==================================================================
     | OUTILS PRIVÉS
     * =================================================================*/

    /**
     * Messages WhatsApp prêts à l'emploi.
     * Les promotions et la fidélisation nécessitent le consentement.
     */
    private function messagesWhatsApp(Client $client): array
    {
        $consent = (bool) $client->consent_promotions;

        $titres = ModeleMessage::pluck('titre', 'type');

        $rdv = DB::table('rendez_vous')
            ->where('client_id', $client->id)
            ->whereIn('statut', ['en_attente', 'confirme'])
            ->where('debut', '>=', now())
            ->orderBy('debut')
            ->first();

        $vars = [
            'prenom' => $client->prenom,
            'nom'    => $client->nom,
        ];

        if ($rdv) {
            $debut = Carbon::parse($rdv->debut);

            $prestation = DB::table('rendez_vous_service')
                ->where('rendez_vous_id', $rdv->id)
                ->pluck('libelle')
                ->implode(', ');

            $vars += [
                'date'       => $debut->translatedFormat('l j F'),
                'heure'      => $debut->format('H:i'),
                'prestation' => $prestation !== ''
                    ? $prestation
                    : 'votre prestation',
            ];
        }

        $offre = trim(
            (string) DB::table('parametres_salon')
                ->where('cle', 'offre_anniversaire')
                ->value('valeur')
        ) ?: null;

        $messages = [];

        foreach (array_keys(WhatsAppService::DEFAUTS) as $type) {

            $texte = $type === 'anniversaire'
                ? WhatsAppService::anniversaire(
                    $client->prenom,
                    $consent ? $offre : null
                )
                : WhatsAppService::rendre(
                    WhatsAppService::gabarit($type),
                    $vars
                );

            $autorise = in_array(
                $type,
                ['promotion', 'fidelisation'],
                true
            ) ? $consent : true;

            $messages[$type] = [
                'titre'    => $titres[$type] ?? ucfirst($type),
                'texte'    => $texte,
                'autorise' => $autorise,
                'raison'   => $autorise
                    ? null
                    : 'pas de consentement aux promotions',
            ];
        }

        $messages['libre'] = [
            'titre'    => 'Message libre',
            'texte'    => "Bonjour {$client->prenom}, ",
            'autorise' => true,
            'raison'   => null,
        ];

        return [
            'messages'    => $messages,
            'rdv_trouve' => (bool) $rdv,
        ];
    }

    /**
     * Requête filtrée partagée par la liste et l'export.
     */
    private function filtered(Request $request): Builder
    {
        $q   = trim((string) $request->q);
        $tab = $request->get('tab', 'tous');
        $tri = $request->get('tri', 'recent');

        $query = Client::query();

        if ($tab === 'archives') {
            $query->whereNotNull('archived_at');
        } else {
            $query->whereNull('archived_at');
        }

        if ($q !== '') {

            $digits = preg_replace('/\D/', '', $q);

            $query->where(function ($w) use ($q, $digits) {

                $w->where(function ($n) use ($q) {

                    foreach (
                        preg_split('/\s+/', $q, -1, PREG_SPLIT_NO_EMPTY)
                        as $mot
                    ) {
                        $n->where(function ($x) use ($mot) {
                            $x->where('prenom', 'like', "%{$mot}%")
                                ->orWhere('nom', 'like', "%{$mot}%");
                        });
                    }
                });

                if ($digits !== '') {
                    $w->orWhere('telephone', 'like', "%{$digits}%")
                        ->orWhere('whatsapp', 'like', "%{$digits}%");
                }

                // Recherche par identifiant : CL-000012 ou CL000012.
                if (preg_match('/^CL-?0*(\d+)$/i', $q, $m)) {
                    $w->orWhere('id', (int) $m[1]);
                }
            });
        }

        if ($tab === 'actifs') {
            $query->where('statut', 'actif');
        }

        if ($tab === 'inactifs') {
            $query->where('statut', 'inactif');
        }

        if ($tab === 'anniversaire') {
            $this->anniversaire($query);
        }

        if ($tri === 'nom') {
            $query->orderBy('nom')->orderBy('prenom');
        } else {
            $query->orderByDesc('id');
        }

        return $query;
    }

    /**
     * Anniversaires des 30 prochains jours.
     */
    private function anniversaire(Builder $query): Builder
    {
        $jours = collect(range(0, 30))
            ->map(fn($i) => now()->startOfDay()->addDays($i)->format('m-d'))
            ->all();

        $expr = DB::getDriverName() === 'sqlite'
            ? "strftime('%m-%d', date_naissance)"
            : "DATE_FORMAT(date_naissance, '%m-%d')";

        return $query
            ->whereNotNull('date_naissance')
            ->whereRaw(
                "{$expr} in (" .
                    implode(',', array_fill(0, count($jours), '?')) .
                    ')',
                $jours
            );
    }

    /**
     * Normalise puis valide les informations.
     *
     * Une seule préférence de communication :
     * - whatsapp
     * - appel
     * - sms
     */
    private function validated(Request $request): array
    {
        $tel = preg_replace(
            '/\D/',
            '',
            (string) $request->telephone
        );

        $canal = $request->input('canal_prefere');

        // Le numéro WhatsApp n'est conservé que si WhatsApp est choisi.
        if ($canal === 'whatsapp') {

            $wa = $request->boolean('whatsapp_same')
                ? $tel
                : preg_replace(
                    '/\D/',
                    '',
                    (string) $request->whatsapp
                );
        } else {
            $wa = null;
        }

        $request->merge([
            'telephone' => $tel,
            'whatsapp'  => $wa !== '' ? $wa : null,
        ]);

        return $request->validate([

            'prenom'         => 'required|string|max:80',
            'nom'            => 'required|string|max:80',

            'public'         => 'nullable|in:femme,homme,enfant',
            'date_naissance' => 'nullable|date|before:today',

            'telephone' => 'required|digits:9',

            'whatsapp' => 'nullable|required_if:canal_prefere,whatsapp|digits:9',

            'email'         => 'nullable|email|max:120',
            'canal_prefere' => 'required|in:whatsapp,appel,sms',

            'notes'  => 'nullable|string|max:1000',
            'statut' => 'required|in:actif,inactif',

        ], [

            'prenom.required' => 'Le prénom est obligatoire.',
            'nom.required'    => 'Le nom est obligatoire.',

            'public.in' => 'Public invalide.',

            'telephone.required' => 'Le téléphone est obligatoire.',
            'telephone.digits' => 'Le numéro de téléphone doit contenir 9 chiffres.',

            'whatsapp.required_if' => 'Le numéro WhatsApp est obligatoire lorsque WhatsApp est choisi.',
            'whatsapp.digits' => 'Le numéro WhatsApp doit contenir 9 chiffres.',

            'canal_prefere.required' => 'Choisissez un moyen de communication.',
            'canal_prefere.in' => 'Le moyen de communication sélectionné est invalide.',

            'email.email' => 'Adresse e-mail invalide.',

            'date_naissance.before' => 'La date de naissance doit être dans le passé.',
        ]);
    }

    /**
     * Recherche un éventuel doublon téléphonique.
     */
    private function doublon(
        string $telephone,
        ?int $ignorerId = null
    ): ?Client {
        return Client::where('telephone', $telephone)
            ->when(
                $ignorerId,
                fn($q) => $q->where('id', '!=', $ignorerId)
            )
            ->first();
    }

    /**
     * Informations du doublon.
     */
    private function infosDoublon(Client $c): array
    {
        return [
            'id'   => $c->id,
            'nom'  => trim($c->prenom . ' ' . $c->nom),
            'code' => $this->code($c),
        ];
    }

    /**
     * Identifiant client.
     */
    private function code(Client $c): string
    {
        return 'CL-' . str_pad(
            $c->id,
            6,
            '0',
            STR_PAD_LEFT
        );
    }

    /**
     * Neutralise l'injection de formules Excel/Sheets.
     */
    private function csvSafe($value): string
    {
        $value = (string) $value;

        return preg_match('/^[=+\-@\t\r]/', $value)
            ? "'" . $value
            : $value;
    }
}
