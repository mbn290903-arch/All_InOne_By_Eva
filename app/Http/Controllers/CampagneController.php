<?php
namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\CampagneMessage;
use App\Models\DestinataireCampagne;
use App\Models\ModeleMessage;
use App\Enums\TypeMessage;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CampagneController extends Controller
{
    private const SUIVIS = [
        'à faire',
        'ouvert dans WhatsApp',
        'réponse reçue',
        'sans réponse',
        'à relancer',
        'ignoré',
    ];

    /**
     * Liste des campagnes et aperçu des destinataires.
     */
    public function index(Request $request)
    {
        $modeles = ModeleMessage::orderBy('titre')->get();

        $modele = $request->filled('modele_message_id')
            ? ModeleMessage::find($request->integer('modele_message_id'))
            : null;

        $clients = collect();
        $valides = collect();
        $invalides = 0;

        if ($modele) {
            $clients = $this->destinatairesPotentiels($request, $modele);

            foreach ($clients as $client) {
                $numero = WhatsAppService::normaliser(
                    $client->whatsapp ?: $client->telephone
                );

                if ($numero) {
                    $client->numero_normalise = $numero;
                    $rdv = $client->rendezVous->first();

                    $client->message_apercu = WhatsAppService::rendre(
                        $modele->contenu,
                        [
                            'prenom' => $client->prenom,
                            'nom' => $client->nom,
                            'date' => $rdv?->debut?->format('d/m/Y') ?? '',
                            'heure' => $rdv?->debut?->format('H:i') ?? '',
                            'prestation' => $rdv?->libellePrestations() ?? '',
                        ]
                    );

                    $valides->push($client);
                } else {
                    $invalides++;
                }
            }
        }

        return view('campagnes.index', compact(
            'modeles',
            'modele',
            'clients',
            'valides',
            'invalides'
        ));
    }

    /**
     * Prépare et enregistre une campagne.
     * Aucun message n'est envoyé automatiquement.
     */
    public function store(Request $request)
    {
        $donnees = $request->validate([
            'titre' => ['required', 'string', 'max:150'],
            'modele_message_id' => ['required', 'exists:modeles_messages,id'],
            'public' => ['nullable', Rule::in(['femme', 'homme', 'enfant'])],
            'statut' => ['nullable', Rule::in(['actif', 'inactif'])],
            'canal_prefere' => ['nullable', Rule::in(['whatsapp', 'appel', 'sms'])],
            'consentement' => ['nullable', Rule::in(['oui', 'non'])],
            'inactifs_jours' => ['nullable', 'integer', 'min:1', 'max:3650'],
            'anniversaires_mois' => ['nullable', 'boolean'],
        ]);

        $modele = ModeleMessage::findOrFail($donnees['modele_message_id']);

        $clients = $this->destinatairesPotentiels($request, $modele);

        $prepares = [];

        foreach ($clients as $client) {
            $numero = WhatsAppService::normaliser(
                $client->whatsapp ?: $client->telephone
            );

            if (! $numero) {
                continue;
            }

            $rdv = $client->rendezVous->first();

            $message = WhatsAppService::rendre(
                $modele->contenu,
                [
                    'prenom' => $client->prenom,
                    'nom' => $client->nom,
                    'date' => $rdv?->debut?->format('d/m/Y') ?? '',
                    'heure' => $rdv?->debut?->format('H:i') ?? '',
                    'prestation' => $rdv?->libellePrestations() ?? '',
                ]
            );

            $prepares[] = [
                'client_id' => $client->id,
                'message_personnalise' => $message,
                'suivi_manuel' => 'à faire',
            ];
        }

        if (count($prepares) === 0) {
            return back()
                ->withInput()
                ->withErrors([
                    'modele_message_id' => 'Aucun destinataire avec un numéro WhatsApp valide ne correspond aux filtres.',
                ]);
        }

        $campagne = DB::transaction(function () use ($donnees, $modele, $prepares) {
            $campagne = CampagneMessage::create([
                'titre' => $donnees['titre'],
                'modele_message_id' => $modele->id,
                'message' => $modele->contenu,
                'statut_suivi' => 'preparee',
                'cree_par' => auth()->id(),
            ]);

            foreach ($prepares as $destinataire) {
                $campagne->destinataires()->create($destinataire);
            }

            return $campagne;
        });

        return redirect()
            ->route('campagnes.show', $campagne)
            ->with('success', count($prepares) . ' destinataire(s) ajouté(s) à la campagne.');
    }

    /**
     * Affiche les destinataires et leur suivi.
     */
    public function show(CampagneMessage $campagne)
    {
        $destinataires = $campagne->destinataires()
            ->with('client')
            ->orderBy('id')
            ->paginate(30);

        foreach ($destinataires as $destinataire) {
            $client = $destinataire->client;

            $destinataire->numero_normalise = $client
                ? WhatsAppService::normaliser($client->whatsapp ?: $client->telephone)
                : null;
        }

        return view('campagnes.show', compact('campagne', 'destinataires'));
    }

    /**
     * Enregistre uniquement la date d'ouverture de WhatsApp.
     */
    public function ouvrir(DestinataireCampagne $destinataire)
    {
        $client = $destinataire->client;

        abort_unless($client, 404);

        $numero = WhatsAppService::normaliser(
            $client->whatsapp ?: $client->telephone
        );

        abort_unless($numero, 422, 'Le numéro WhatsApp de ce client est invalide.');

        $lien = WhatsAppService::lien(
            $numero,
            $destinataire->message_personnalise
        );

        $destinataire->update([
            'ouvert_le' => now(),
        ]);

        return redirect()->away($lien);
    }

    /**
     * Modifie manuellement le suivi d'un destinataire.
     */
    public function suivi(Request $request, DestinataireCampagne $destinataire)
    {
        $donnees = $request->validate([
            'suivi_manuel' => ['required', Rule::in(self::SUIVIS)],
        ]);

        $destinataire->update($donnees);

        return back()->with('success', 'Suivi du destinataire mis à jour.');
    }

    /**
     * Construit la liste des clients correspondant aux filtres.
     */
    private function destinatairesPotentiels(
        Request $request,
        ModeleMessage $modele
    ) {
        $type = $modele->type instanceof TypeMessage
            ? $modele->type->value
            : (string) $modele->type;

        $query = Client::query()
            ->nonArchives()
            ->with([
                'rendezVous' => function ($rdv) {
                    $rdv->with('lignes')
                        ->whereIn('statut', ['en_attente', 'confirme'])
                        ->where('debut', '>=', now())
                        ->orderBy('debut');
                },
            ]);

        // Promotion et fidélisation : consentement obligatoire.
        if (in_array($type, ['promotion', 'fidelisation'], true)) {
            $query->where('consent_promotions', true);
        } elseif ($request->consentement === 'oui') {
            $query->where('consent_promotions', true);
        } elseif ($request->consentement === 'non') {
            $query->where('consent_promotions', false);
        }

        if ($request->filled('public')) {
            $query->where('public', $request->public);
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('canal_prefere')) {
            $query->where('canal_prefere', $request->canal_prefere);
        }

        // Clients sans visite terminée au cours des N derniers jours.
        if ($request->filled('inactifs_jours')) {
            $dateLimite = now()->subDays((int) $request->inactifs_jours);

            $query->whereDoesntHave('rendezVous', function ($rdv) use ($dateLimite) {
                $rdv->where('statut', 'termine')
                    ->where('debut', '>=', $dateLimite);
            });
        }

        // Anniversaires du mois en cours.
        if ($request->boolean('anniversaires_mois') || $type === 'anniversaire') {
            $query->whereNotNull('date_naissance')
                ->whereMonth('date_naissance', now()->month);
        }

        // Un rappel nécessite un rendez-vous futur.
        if ($type === 'rappel_rdv') {
            $query->whereHas('rendezVous', function ($rdv) {
                $rdv->whereIn('statut', ['en_attente', 'confirme'])
                    ->where('debut', '>=', now());
            });
        }

        return $query->orderBy('nom')
            ->orderBy('prenom')
            ->get();
    }
}
