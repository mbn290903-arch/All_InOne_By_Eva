<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnniversaireController extends Controller
{
    private const CLE_OFFRE = 'offre_anniversaire';
    private const ONGLETS   = ['aujourdhui', 'semaine', 'mois', 'sans-date'];

    /* ------------------------------------------------------------------
     |  PAGE
     * ----------------------------------------------------------------*/
    public function index(Request $request)
    {
        $this->autoriser();

        $tab        = in_array($request->get('tab'), self::ONGLETS, true) ? $request->get('tab') : 'aujourdhui';
        $aujourdhui = now()->startOfDay();
        $moisDebut  = $aujourdhui->copy()->startOfMonth();
        $moisFin    = $aujourdhui->copy()->endOfMonth()->startOfDay();
        $semaineFin = $aujourdhui->copy()->addDays(6);
        $offre      = $this->offre();
        $q          = trim((string) $request->q);

        // Calendrier utile : tout le mois en cours + 7 jours à partir d'aujourd'hui.
        // Clé = « mois-jour » : le client est détecté quelle que soit son année de naissance.
        $parMd = [];
        $ajouter = function ($date) use (&$parMd) {
            $parMd[$date->format('m-d')] = $date;
            // Né un 29 février : fêté le 28 février les années non bissextiles.
            if ($date->format('m-d') === '02-28' && ! $date->isLeapYear()) {
                $parMd['02-29'] = $date;
            }
        };
        for ($i = 0; $i < $aujourdhui->daysInMonth; $i++) { $ajouter($moisDebut->copy()->addDays($i)); }
        for ($i = 0; $i <= 6; $i++)                       { $ajouter($aujourdhui->copy()->addDays($i)); }

        $mdListe = array_keys($parMd);
        $expr    = DB::getDriverName() === 'sqlite'
            ? "strftime('%m-%d', date_naissance)"
            : "DATE_FORMAT(date_naissance, '%m-%d')";

        $candidats = $this->rechercher(
            Client::query()
                ->whereNull('archived_at')
                ->whereNotNull('date_naissance')
                ->whereRaw("{$expr} in (" . implode(',', array_fill(0, count($mdListe), '?')) . ')', $mdListe),
            $q
        )->get();

        // Anniversaires déjà traités (client + année de l'anniversaire)
        $traites = DB::table('anniversaires_traites')
            ->whereIn('client_id', $candidats->pluck('id'))
            ->whereIn('annee', [now()->year, now()->year + 1])
            ->get(['client_id', 'annee'])
            ->mapWithKeys(fn($t) => [$t->client_id . '-' . $t->annee => true]);

        $tous = $candidats->map(function ($c) use ($parMd, $aujourdhui, $offre, $traites) {
            $date      = $parMd[$c->date_naissance->format('m-d')];
            $avecOffre = $offre !== null && $c->consent_promotions;
            $message   = $this->message($c->prenom, $avecOffre ? $offre : null);

            return (object) [
                'client'     => $c,
                'date'       => $date,
                'annee'      => $date->year,
                'age'        => $date->year - $c->date_naissance->year,
                'jours'      => (int) $aujourdhui->diffInDays($date, false),
                'traite'     => isset($traites[$c->id . '-' . $date->year]),
                'avec_offre' => $avecOffre,
                'wa_url'     => WhatsAppService::lien($c->whatsapp ?: $c->telephone, $message),
            ];
        });

        $filtres = [
            'aujourdhui' => fn($l) => $l->date->isSameDay($aujourdhui),
            'semaine'    => fn($l) => $l->date->between($aujourdhui, $semaineFin),
            'mois'       => fn($l) => $l->date->between($moisDebut, $moisFin),
        ];

        $sansDate = $this->rechercher(Client::query()->whereNull('archived_at')->whereNull('date_naissance'), $q);

        $compteurs = [
            'aujourdhui' => $tous->filter($filtres['aujourdhui'])->count(),
            'semaine'    => $tous->filter($filtres['semaine'])->count(),
            'mois'       => $tous->filter($filtres['mois'])->count(),
            'sans-date'  => (clone $sansDate)->count(),
        ];

        $lignes = collect();
        $sansDatePage = null;

        if ($tab === 'sans-date') {
            $sansDatePage = $sansDate->orderBy('nom')->orderBy('prenom')->paginate(20)->withQueryString();
        } else {
            $lignes = $tous->filter($filtres[$tab])
                ->sortBy(fn($l) => [$l->date->timestamp, mb_strtolower($l->client->nom)])
                ->values();
        }

        return view('anniversaires.index', [
            'tab'          => $tab,
            'q'            => $q,
            'lignes'       => $lignes,
            'sansDatePage' => $sansDatePage,
            'compteurs'    => $compteurs,
            'offre'        => $offre,
            'peutOffre'    => auth()->user()->isAdmin(),
        ]);
    }

    /* ------------------------------------------------------------------
     |  OFFRE D'ANNIVERSAIRE (administratrice uniquement, texte libre)
     * ----------------------------------------------------------------*/
    public function enregistrerOffre(Request $request)
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $data  = $request->validate(['offre' => 'nullable|string|max:200']);
        $texte = trim((string) ($data['offre'] ?? ''));

        $existe = DB::table('parametres_salon')->where('cle', self::CLE_OFFRE)->exists();

        $existe
            ? DB::table('parametres_salon')->where('cle', self::CLE_OFFRE)->update(['valeur' => $texte ?: null, 'updated_at' => now()])
            : DB::table('parametres_salon')->insert(['cle' => self::CLE_OFFRE, 'valeur' => $texte ?: null, 'created_at' => now(), 'updated_at' => now()]);

        return back()->with('status', [
            $texte !== '' ? 'Offre enregistrée' : 'Offre retirée',
            $texte !== '' ? "Offre d'anniversaire : {$texte}" : "Les messages d'anniversaire seront envoyés sans offre.",
        ]);
    }

    /* ------------------------------------------------------------------
     |  TRAITÉ / NON TRAITÉ (un seul traitement par client et par année)
     * ----------------------------------------------------------------*/
    public function traiter(Request $request, Client $client)
    {
        $this->autoriser();
        $annee = $this->anneeValide($request);

        DB::table('anniversaires_traites')->insertOrIgnore([
            'client_id'  => $client->id,
            'annee'      => $annee,
            'offre'      => $client->consent_promotions ? $this->offre() : null,
            'traite_par' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('status', ['Anniversaire traité', "{$client->prenom} {$client->nom} : marqué comme traité pour {$annee}."]);
    }

    public function annuler(Request $request, Client $client)
    {
        $this->autoriser();
        $annee = $this->anneeValide($request);

        DB::table('anniversaires_traites')->where('client_id', $client->id)->where('annee', $annee)->delete();

        return back()->with('status', ['Traitement annulé', "{$client->prenom} {$client->nom} : à traiter de nouveau."]);
    }

    /* ------------------------------------------------------------------
     |  OUTILS
     * ----------------------------------------------------------------*/
    /** Filtre par nom (chaque mot dans le prénom ou le nom), ou par téléphone / WhatsApp. */
    private function rechercher($query, string $q)
    {
        if ($q === '') {
            return $query;
        }

        $digits = preg_replace('/\D/', '', $q);

        return $query->where(function ($w) use ($q, $digits) {
            $w->where(function ($n) use ($q) {
                foreach (preg_split('/\s+/', $q, -1, PREG_SPLIT_NO_EMPTY) as $mot) {
                    $n->where(fn($x) => $x->where('prenom', 'like', "%{$mot}%")->orWhere('nom', 'like', "%{$mot}%"));
                }
            });

            if ($digits !== '') {
                $w->orWhere('telephone', 'like', "%{$digits}%")->orWhere('whatsapp', 'like', "%{$digits}%");
            }
        });
    }

    private function autoriser(): void
    {
        $u = auth()->user();
        abort_unless($u->isAdmin() || $u->hasPermission('whatsapp.envoyer'), 403);
    }

    private function anneeValide(Request $request): int
    {
        return (int) $request->validate([
            'annee' => ['required', 'integer', 'in:' . now()->year . ',' . (now()->year + 1)],
        ])['annee'];
    }

    private function offre(): ?string
    {
        $v = trim((string) DB::table('parametres_salon')->where('cle', self::CLE_OFFRE)->value('valeur'));

        return $v !== '' ? $v : null;
    }

    /** Message WhatsApp : modèle « anniversaire » modifiable dans Paramètres. L'offre n'est ajoutée que si la cliente a consenti aux promotions. */
    private function message(string $prenom, ?string $offre): string
    {
        return WhatsAppService::anniversaire($prenom, $offre);
    }
}
