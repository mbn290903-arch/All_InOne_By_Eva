@extends('layouts.app')

@section('title', 'Programme de fidélité — All in One by Eva')
@section('page-title', 'Programme de fidélité')

@push('styles')
<style>
    .loyalty-panel {
        background: white;
        border: 1px solid #eae5e2;
        border-radius: 11px;
        box-shadow: 0 1px 3px rgba(40, 20, 25, .035);
    }

    .loyalty-label {
        color: #817a7e;
        font-size: 11px;
    }

    .loyalty-value {
        color: #292327;
        font-size: 25px;
        font-weight: 700;
        letter-spacing: -.04em;
    }

    .loyalty-icon {
        display: grid;
        place-items: center;
        width: 34px;
        height: 34px;
        border-radius: 9px;
        background: #f7ecef;
        color: #76243d;
    }

    .loyalty-icon svg {
        width: 17px;
        height: 17px;
    }

    .loyalty-section {
        color: #827a7f;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .12em;
    }

    .loyalty-tab {
        border: 1px solid transparent;
        border-radius: 7px;
        padding: 9px 13px;
        color: #817a7e;
        font-size: 12px;
        font-weight: 500;
    }

    .loyalty-tab.active {
        background: #f7ecef;
        color: #76243d;
        border-color: #ead7de;
        font-weight: 700;
    }

    .loyalty-input {
        width: 100%;
        border: 1px solid #e5dfdc;
        border-radius: 7px;
        padding: 9px 11px;
        font-size: 12px;
        outline: none;
        background: white;
    }

    .loyalty-input:focus {
        border-color: #76243d;
        box-shadow: 0 0 0 2px rgba(118, 36, 61, .08);
    }

    .loyalty-primary {
        background: #76243d;
        color: white;
        border-radius: 7px;
        padding: 9px 13px;
        font-size: 12px;
        font-weight: 600;
    }

    .loyalty-primary:hover {
        background: #5d1830;
    }

    .loyalty-secondary {
        border: 1px solid #e5dfdc;
        background: white;
        color: #51484d;
        border-radius: 7px;
        padding: 9px 13px;
        font-size: 12px;
        font-weight: 500;
    }

    .loyalty-table th {
        color: #827a7f;
        font-size: 10px;
        font-weight: 600;
        text-align: left;
        padding: 12px;
        background: #faf8f7;
        white-space: nowrap;
    }

    .loyalty-table td {
        padding: 13px 12px;
        border-top: 1px solid #f0ecea;
        font-size: 11px;
        vertical-align: middle;
    }

    .loyalty-row:hover {
        background: #fcfafb;
    }

    .loyalty-badge {
        display: inline-flex;
        align-items: center;
        border-radius: 20px;
        padding: 4px 8px;
        font-size: 9px;
        font-weight: 600;
    }

    .loyalty-positive {
        background: #eaf5ee;
        color: #34734b;
    }

    .loyalty-negative {
        background: #fff0ed;
        color: #b34c3e;
    }

    .loyalty-muted {
        background: #f1efee;
        color: #71696e;
    }

    .loyalty-tab-content[hidden] {
        display: none !important;
    }

    .loyalty-client {
        cursor: pointer;
    }

    .loyalty-client:hover {
        color: #76243d;
        text-decoration: underline;
    }

    @media (max-width: 640px) {
        .loyalty-tabs {
            overflow-x: auto;
        }

        .loyalty-value {
            font-size: 21px;
        }
    }
</style>
@endpush

@section('content')

<div class="space-y-5">

    {{-- Avertissement --}}
    <div class="flex flex-wrap items-center justify-between gap-3 rounded-lg border border-[#f0dfc7] bg-[#fff8ed] px-4 py-3">
        <div class="flex items-center gap-2 text-[11px] text-[#94621f]">
            <i data-lucide="flask-conical" class="h-4 w-4"></i>
            <span class="font-semibold">Programme connecté</span>
            <span class="hidden sm:inline">— Données issues de votre base.</span>
        </div>
        <span class="text-[10px] text-[#94621f]">Suivi réel des points</span>
    </div>

    {{-- Introduction --}}
    <section class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <div class="mb-1 text-[11px] font-medium uppercase tracking-widest text-[#9a858e]">
                RELATION CLIENT
            </div>
            <h2 class="text-[21px] font-semibold tracking-tight">Programme de fidélité</h2>
            <p class="mt-1 text-[11px] text-muted">
                Récompensez les clients et encouragez leur retour chez All in One by Eva.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <span class="loyalty-badge loyalty-positive">
                <span class="mr-1 h-1.5 w-1.5 rounded-full bg-[#34734b]"></span>
                Programme activé · Démo
            </span>
        </div>
    </section>

    {{-- Indicateurs --}}
    <section class="grid grid-cols-2 gap-3 xl:grid-cols-4">

        <div class="loyalty-panel p-4">
            <div class="mb-3 flex items-center justify-between">
                <span class="loyalty-label">Clients fidélisés</span>
                <div class="loyalty-icon"><i data-lucide="users"></i></div>
            </div>
            <div class="loyalty-value">{{ number_format($clientsFidelises, 0, ',', ' ') }}</div>
            <p class="mt-2 text-[10px] text-[#34734b]">+ 28 ce mois-ci</p>
        </div>

        <div class="loyalty-panel p-4">
            <div class="mb-3 flex items-center justify-between">
                <span class="loyalty-label">Points distribués</span>
                <div class="loyalty-icon"><i data-lucide="gem"></i></div>
            </div>
            <div class="loyalty-value">{{ number_format($pointsDistribues, 0, ',', ' ') }}</div>
            <p class="mt-2 text-[10px] text-muted">Depuis le lancement</p>
        </div>

        <div class="loyalty-panel p-4">
            <div class="mb-3 flex items-center justify-between">
                <span class="loyalty-label">Récompenses actives</span>
                <div class="loyalty-icon"><i data-lucide="gift"></i></div>
            </div>
            <div class="loyalty-value">{{ $recompenses->where('is_active', true)->count() }}</div>
            <p class="mt-2 text-[10px] text-muted">Dans le catalogue</p>
        </div>

        <div class="loyalty-panel p-4">
            <div class="mb-3 flex items-center justify-between">
                <span class="loyalty-label">Récompenses utilisées</span>
                <div class="loyalty-icon"><i data-lucide="ticket-check"></i></div>
            </div>
            <div class="loyalty-value">{{ number_format($recompensesUtilisees, 0, ',', ' ') }}</div>
            <p class="mt-2 text-[10px] text-[#34734b]">Ce mois-ci</p>
        </div>

    </section>

    {{-- Navigation --}}
    <section class="loyalty-panel p-2">
        <div class="loyalty-tabs flex flex-wrap gap-1" role="tablist">
            <button type="button" class="loyalty-tab active" data-tab="overview">
                <i data-lucide="layout-dashboard" class="mr-1 inline h-3.5 w-3.5"></i>
                Vue d'ensemble
            </button>
            <button type="button" class="loyalty-tab" data-tab="clients">
                <i data-lucide="users" class="mr-1 inline h-3.5 w-3.5"></i>
                Clients
            </button>
            <button type="button" class="loyalty-tab" data-tab="rules">
                <i data-lucide="sliders-horizontal" class="mr-1 inline h-3.5 w-3.5"></i>
                Paramètres
            </button>
            <button type="button" class="loyalty-tab" data-tab="rewards">
                <i data-lucide="gift" class="mr-1 inline h-3.5 w-3.5"></i>
                Récompenses
            </button>
            <button type="button" class="loyalty-tab" data-tab="history">
                <i data-lucide="history" class="mr-1 inline h-3.5 w-3.5"></i>
                Historique
            </button>
        </div>
    </section>

    {{-- VUE D'ENSEMBLE --}}
    <div id="tab-overview" class="loyalty-tab-content space-y-5">

        <section class="grid gap-4 xl:grid-cols-3">

            <div class="loyalty-panel p-5 xl:col-span-2">
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h3 class="text-[13px] font-semibold">Règle actuelle</h3>
                        <p class="mt-1 text-[10px] text-muted">Règle appliquée aux nouveaux paiements</p>
                    </div>
                    @if ($regleActuelle)
                    <span class="loyalty-badge loyalty-positive">En vigueur</span>
                    @else
                    <span class="loyalty-badge loyalty-muted">Aucune règle</span>
                    @endif
                </div>

                @if ($regleActuelle)
                @php
                    $donneesRegleActuelle = [
                        'url' => route('fidelite.regles.update', $regleActuelle),
                        'libelle' => $regleActuelle->libelle,
                        'montant_par_tranche' => $regleActuelle->montant_par_tranche,
                        'points_par_tranche' => $regleActuelle->points_par_tranche,
                        'date_debut' => $regleActuelle->date_debut->format('Y-m-d'),
                        'date_fin' => $regleActuelle->date_fin?->format('Y-m-d'),
                        'is_active' => (bool) $regleActuelle->is_active,
                    ];
                @endphp
                <div class="rounded-lg bg-[#faf6f7] p-4">
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="grid h-11 w-11 place-items-center rounded-xl bg-[#f0dfe4] text-wine">
                            <i data-lucide="coins" class="h-5 w-5"></i>
                        </div>
                        <div>
                            <div class="text-[18px] font-bold text-wine">
                                {{ $regleActuelle->points_par_tranche }} point(s)
                            </div>
                            <div class="text-[11px] text-muted">
                                Pour chaque tranche de {{ number_format($regleActuelle->montant_par_tranche, 0, ',', ' ') }} FCFA payée
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 grid grid-cols-2 gap-3 border-t border-[#eadde1] pt-3">
                        <div>
                            <div class="loyalty-label">Nom de la règle</div>
                            <div class="mt-1 text-[13px] font-semibold">{{ $regleActuelle->libelle }}</div>
                        </div>
                        <div>
                            <div class="loyalty-label">Période</div>
                            <div class="mt-1 text-[13px] font-semibold">
                                Depuis le {{ $regleActuelle->date_debut->format('d/m/Y') }}
                                @if ($regleActuelle->date_fin)
                                jusqu'au {{ $regleActuelle->date_fin->format('d/m/Y') }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap gap-2">
                    <button type="button" class="loyalty-primary" data-edit-regle="{{ json_encode($donneesRegleActuelle) }}">
                        <i data-lucide="pencil" class="mr-1 inline h-3.5 w-3.5"></i>
                        Modifier la règle
                    </button>
                </div>
                @else
                <div class="rounded-lg bg-[#faf6f7] p-4 text-[11px] text-muted">
                    Aucune règle n'est en vigueur. Créez-en une dans l'onglet Paramètres.
                </div>
                <button type="button" data-open-tab="rules" class="loyalty-primary mt-4">
                    Créer une règle
                    <i data-lucide="arrow-right" class="ml-1 inline h-3.5 w-3.5"></i>
                </button>
                @endif
            </div>

            <div class="loyalty-panel p-5">
                <h3 class="text-[13px] font-semibold">Activité récente</h3>
                <p class="mb-4 mt-1 text-[10px] text-muted">Exemples de mouvements</p>

                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="loyalty-icon"><i data-lucide="plus"></i></div>
                        <div class="min-w-0 flex-1">
                            <div class="text-[11px] font-semibold">Aminata Ndiaye</div>
                            <div class="text-[10px] text-muted">Points gagnés</div>
                        </div>
                        <span class="text-[11px] font-semibold text-[#34734b]">+ 25 pts</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="loyalty-icon"><i data-lucide="gift"></i></div>
                        <div class="min-w-0 flex-1">
                            <div class="text-[11px] font-semibold">Fatou Diop</div>
                            <div class="text-[10px] text-muted">Récompense utilisée</div>
                        </div>
                        <span class="text-[11px] font-semibold text-[#b34c3e]">− 100 pts</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="loyalty-icon"><i data-lucide="plus"></i></div>
                        <div class="min-w-0 flex-1">
                            <div class="text-[11px] font-semibold">Moussa Fall</div>
                            <div class="text-[10px] text-muted">Points gagnés</div>
                        </div>
                        <span class="text-[11px] font-semibold text-[#34734b]">+ 18 pts</span>
                    </div>
                </div>
            </div>

        </section>

        <section class="loyalty-panel overflow-hidden">
            <div class="flex flex-wrap items-center justify-between gap-2 p-4">
                <div>
                    <h3 class="text-[13px] font-semibold">Clients les plus fidèles</h3>
                    <p class="mt-1 text-[10px] text-muted">Exemples de soldes de points</p>
                </div>
                <button type="button" data-open-tab="clients" class="text-[11px] font-semibold text-wine">
                    Voir tous les clients →
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="loyalty-table w-full">
                    <thead>
                        <tr>
                            <th>CLIENT</th>
                            <th>SOLDE</th>
                            <th>RÉCOMPENSES</th>
                            <th>DERNIÈRE ACTIVITÉ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="loyalty-row">
                            <td class="font-semibold">Aminata Ndiaye</td>
                            <td>245 pts</td>
                            <td>2 disponibles</td>
                            <td>10 nov. 2026</td>
                        </tr>
                        <tr class="loyalty-row">
                            <td class="font-semibold">Fatou Diop</td>
                            <td>180 pts</td>
                            <td>1 disponible</td>
                            <td>09 nov. 2026</td>
                        </tr>
                        <tr class="loyalty-row">
                            <td class="font-semibold">Moussa Fall</td>
                            <td>95 pts</td>
                            <td>0 disponible</td>
                            <td>08 nov. 2026</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

    </div>

    {{-- CLIENTS --}}
    <div id="tab-clients" class="loyalty-tab-content space-y-4" hidden>

        <section class="loyalty-panel p-4">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h3 class="text-[13px] font-semibold">Portefeuille fidélité</h3>
                    <p class="mt-1 text-[10px] text-muted">Sélectionnez un client pour ouvrir sa fiche.</p>
                </div>
                <input id="client-search" class="loyalty-input max-w-[260px]" placeholder="Rechercher un client...">
            </div>

            <div class="overflow-x-auto">
                <table class="loyalty-table w-full">
                    <thead>
                        <tr>
                            <th>CLIENT</th>
                            <th>POINTS DISPONIBLES</th>
                            <th>POINTS GAGNÉS</th>
                            <th>RÉCOMPENSES</th>
                            <th>DERNIÈRE VISITE</th>
                        </tr>
                    </thead>
                    <tbody id="client-list">
                        @forelse ($clients as $client)
                        @php
                            $derniereOperation = $client->mouvementsPoints->sortByDesc('created_at')->first();
                            $nbRecompensesDisponibles = $recompenses
                                ->where('is_active', true)
                                ->where('seuil_points', '<=', $client->solde_points)
                                ->count();
                        @endphp
                        <tr class="loyalty-row" data-client="{{ $client->nomComplet }}">
                            <td>
                                <button
                                    type="button"
                                    class="loyalty-client font-semibold"
                                    data-client-detail="{{ $client->id }}">
                                    {{ $client->nomComplet }}
                                </button>
                            </td>
                            <td class="font-semibold text-wine">
                                {{ number_format($client->solde_points, 0, ',', ' ') }} pts
                            </td>
                            <td>
                                {{ number_format($client->points_gagnes, 0, ',', ' ') }} pts
                            </td>
                            <td>{{ $nbRecompensesDisponibles }}</td>
                            <td>{{ $derniereOperation?->created_at?->format('d/m/Y') ?? '—' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-5 text-center text-muted">
                                Aucun client enregistré.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        {{-- Fiche individuelle --}}
        <section id="client-detail" class="loyalty-panel p-5" hidden>
            <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                <div>
                    <div class="loyalty-section">FICHE CLIENT</div>
                    <h3 id="detail-name" class="mt-1 text-[17px] font-semibold"></h3>
                </div>
                <button type="button" id="close-client-detail" class="loyalty-secondary">
                    <i data-lucide="x" class="mr-1 inline h-3.5 w-3.5"></i>
                    Fermer la fiche
                </button>
            </div>

            <div class="mb-5 grid grid-cols-2 gap-3 md:grid-cols-4">
                <div class="rounded-lg bg-[#faf6f7] p-3">
                    <div class="loyalty-label">Solde actuel</div>
                    <div id="detail-balance" class="mt-1 text-[20px] font-bold text-wine">0 pts</div>
                </div>
                <div class="rounded-lg bg-[#faf6f7] p-3">
                    <div class="loyalty-label">Total gagné</div>
                    <div id="detail-earned" class="mt-1 text-[20px] font-bold">0 pts</div>
                </div>
                <div class="rounded-lg bg-[#faf6f7] p-3">
                    <div class="loyalty-label">Total utilisé</div>
                    <div id="detail-used" class="mt-1 text-[20px] font-bold">0 pts</div>
                </div>
                <div class="rounded-lg bg-[#faf6f7] p-3">
                    <div class="loyalty-label">Récompenses</div>
                    <div id="detail-rewards" class="mt-1 text-[20px] font-bold">0</div>
                </div>
            </div>

            <h4 class="mb-3 text-[12px] font-semibold">Historique du client</h4>
            <div class="overflow-x-auto">
                <table class="loyalty-table w-full">
                    <thead>
                        <tr>
                            <th>DATE</th>
                            <th>OPÉRATION</th>
                            <th>RÉFÉRENCE</th>
                            <th>POINTS</th>
                        </tr>
                    </thead>
                    <tbody id="detail-history"></tbody>
                </table>
            </div>
        </section>

    </div>

    {{-- PARAMÈTRES --}}
    <div id="tab-rules" class="loyalty-tab-content space-y-4" hidden>

        <section class="loyalty-panel p-5">
            <div class="mb-4">
                <h3 class="text-[13px] font-semibold">
                    Règles d'accumulation des points
                    <span id="regle-mode" class="loyalty-badge loyalty-muted ml-2" hidden>Modification en cours</span>
                </h3>
                <p class="mt-1 text-[10px] text-muted">
                    Définissez combien de points un client gagne après une prestation payée.
                </p>
            </div>

            <form id="regle-form" method="POST" action="{{ route('fidelite.regles.store') }}" data-store-url="{{ route('fidelite.regles.store') }}">
                @csrf
                <input type="hidden" name="_method" value="PUT" id="regle-method" disabled>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-[11px] font-medium">Nom de la règle</label>
                        <input class="loyalty-input" type="text" name="libelle" maxlength="120" placeholder="Ex. Règle standard" required>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-[11px] font-medium">Montant dépensé (FCFA)</label>
                        <input class="loyalty-input" type="number" name="montant_par_tranche" min="1" value="1000" required>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-[11px] font-medium">Points attribués</label>
                        <input class="loyalty-input" type="number" name="points_par_tranche" min="1" value="1" required>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-[11px] font-medium">Début d'application</label>
                        <input class="loyalty-input" type="date" name="date_debut" value="{{ now()->toDateString() }}" required>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-[11px] font-medium">Fin d'application (facultatif)</label>
                        <input class="loyalty-input" type="date" name="date_fin">
                    </div>
                </div>

                <div id="regle-active-wrap" class="mt-4" hidden>
                    <label class="flex items-center gap-2 text-[11px] font-medium">
                        <input type="checkbox" name="is_active" value="1" id="regle-active" disabled>
                        Règle activée
                    </label>
                </div>

                <div class="mt-4 rounded-lg border border-[#eee3e7] bg-[#faf6f7] p-3 text-[10px] leading-5 text-[#725965]">
                    <i data-lucide="info" class="mr-1 inline h-3.5 w-3.5"></i>
                    Une nouvelle règle s'applique uniquement aux paiements futurs. Les anciens points conservent toujours la règle utilisée au moment de leur attribution.
                </div>

                <div class="mt-4 flex flex-wrap justify-end gap-2">
                    <button type="reset" class="loyalty-secondary">Annuler</button>
                    <button type="submit" class="loyalty-primary">
                        <i data-lucide="save" class="mr-1 inline h-3.5 w-3.5"></i>
                        <span id="regle-submit-label">Enregistrer la règle</span>
                    </button>
                </div>
            </form>

            @if (session('success'))
            <p class="mt-3 text-right text-[10px] text-[#34734b]">{{ session('success') }}</p>
            @endif

            @if ($errors->any())
            <div class="mt-3 text-right text-[10px] text-red-600">
                @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
                @endforeach
            </div>
            @endif
        </section>

        <section class="loyalty-panel p-5">
            <h3 class="mb-3 text-[13px] font-semibold">Périodes et règles enregistrées</h3>
            <div class="overflow-x-auto">
                <table class="loyalty-table w-full">
                    <thead>
                        <tr>
                            <th>PÉRIODE</th>
                            <th>RÈGLE</th>
                            <th>STATUT</th>
                            <th>ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($regles as $regle)
                        @php
                            $donneesRegle = [
                                'url' => route('fidelite.regles.update', $regle),
                                'libelle' => $regle->libelle,
                                'montant_par_tranche' => $regle->montant_par_tranche,
                                'points_par_tranche' => $regle->points_par_tranche,
                                'date_debut' => $regle->date_debut->format('Y-m-d'),
                                'date_fin' => $regle->date_fin?->format('Y-m-d'),
                                'is_active' => (bool) $regle->is_active,
                            ];
                        @endphp
                        <tr>
                            <td class="font-semibold">
                                {{ $regle->libelle }}
                                <div class="mt-1 text-[10px] text-muted">
                                    Du {{ $regle->date_debut->format('d/m/Y') }}
                                    @if ($regle->date_fin)
                                    au {{ $regle->date_fin->format('d/m/Y') }}
                                    @else
                                    · Sans date de fin
                                    @endif
                                </div>
                            </td>
                            <td>
                                {{ $regle->points_par_tranche }} point(s) /
                                {{ number_format($regle->montant_par_tranche, 0, ',', ' ') }} FCFA
                            </td>
                            <td>
                                @if (! $regle->is_active)
                                <span class="loyalty-badge loyalty-muted">Désactivée</span>
                                @elseif ((int) $regle->id === (int) ($regleActuelle?->id ?? 0))
                                <span class="loyalty-badge loyalty-positive">En vigueur</span>
                                @elseif ($regle->date_debut->isFuture())
                                <span class="loyalty-badge loyalty-muted">À venir</span>
                                @elseif ($regle->date_fin && $regle->date_fin->isPast())
                                <span class="loyalty-badge loyalty-muted">Expirée</span>
                                @else
                                <span class="loyalty-badge loyalty-muted">Activée</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <button type="button" class="loyalty-secondary" data-edit-regle="{{ json_encode($donneesRegle) }}">
                                        Modifier
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-5 text-center text-muted">
                                Aucune règle enregistrée pour le moment.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="loyalty-panel p-5">
            <h3 class="mb-2 text-[13px] font-semibold">Corrections et sécurité</h3>
            <p class="text-[11px] leading-5 text-muted">
                Toute correction de points devra être réservée aux personnes autorisées et accompagnée d'un motif. Une correction créera une nouvelle opération dans l'historique au lieu d'effacer l'ancienne.
            </p>
            <div class="mt-3 flex items-center gap-2 text-[10px] font-medium text-wine">
                <i data-lucide="shield-check" class="h-4 w-4"></i>
                Historique traçable · Accès administrateur
            </div>
        </section>

    </div>

    {{-- RÉCOMPENSES --}}
    <div id="tab-rewards" class="loyalty-tab-content space-y-4" hidden>

        <section class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h3 class="text-[13px] font-semibold">Catalogue des récompenses</h3>
                <p class="mt-1 text-[10px] text-muted">Avantages échangeables contre des points.</p>
            </div>
        </section>

        <section class="loyalty-panel p-5">
            <h3 class="mb-4 text-[13px] font-semibold">Ajouter une récompense</h3>

            <form method="POST" action="{{ route('fidelite.recompenses.store') }}">
                @csrf

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-[11px] font-medium">Nom de la récompense</label>
                        <input class="loyalty-input" type="text" name="libelle" maxlength="255" placeholder="Ex. Remise de 5 000 FCFA" required>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-[11px] font-medium">Type de récompense</label>
                        <select class="loyalty-input" name="type" required>
                            <option value="reduction">Réduction</option>
                            <option value="prestation_offerte">Prestation offerte</option>
                            <option value="offre_speciale">Offre spéciale</option>
                        </select>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-[11px] font-medium">Seuil de points nécessaire</label>
                        <input class="loyalty-input" type="number" name="seuil_points" min="1" required>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-[11px] font-medium">Valeur (FCFA, si applicable)</label>
                        <input class="loyalty-input" type="number" name="valeur" min="0" value="0">
                    </div>
                </div>

                <div class="mt-4 flex justify-end">
                    <button type="submit" class="loyalty-primary">
                        <i data-lucide="plus" class="mr-1 inline h-3.5 w-3.5"></i>
                        Enregistrer la récompense
                    </button>
                </div>
            </form>

            @if ($errors->any())
            <div class="mt-3 text-[10px] text-red-600">
                @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
                @endforeach
            </div>
            @endif
        </section>

        @if (session('success'))
        <p class="text-[10px] text-[#34734b]">{{ session('success') }}</p>
        @endif

        <section class="grid gap-3 md:grid-cols-3">
            @forelse ($recompenses as $recompense)
            <article class="loyalty-panel p-5">
                <div class="mb-4 flex h-10 w-10 items-center justify-center rounded-xl bg-[#f7ecef] text-wine">
                    @if ($recompense->type->value === 'reduction')
                    <i data-lucide="badge-percent" class="h-5 w-5"></i>
                    @elseif ($recompense->type->value === 'prestation_offerte')
                    <i data-lucide="sparkles" class="h-5 w-5"></i>
                    @else
                    <i data-lucide="crown" class="h-5 w-5"></i>
                    @endif
                </div>

                @if ($recompense->is_active)
                <span class="loyalty-badge loyalty-positive">Disponible</span>
                @else
                <span class="loyalty-badge loyalty-muted">Désactivée</span>
                @endif

                <h4 class="mt-3 text-[14px] font-semibold">{{ $recompense->libelle }}</h4>

                <p class="mt-1 text-[10px] text-muted">
                    @if ($recompense->type->value === 'reduction')
                    Réduction sur une prochaine visite.
                    @elseif ($recompense->type->value === 'prestation_offerte')
                    Prestation offerte au client.
                    @else
                    Offre spéciale réservée aux clients fidèles.
                    @endif
                </p>

                @if ($recompense->valeur > 0)
                <p class="mt-3 text-[11px] text-muted">
                    Valeur : {{ number_format($recompense->valeur, 0, ',', ' ') }} FCFA
                </p>
                @endif

                <div class="mt-4 border-t border-line pt-3 text-[17px] font-bold text-wine">
                    {{ number_format($recompense->seuil_points, 0, ',', ' ') }} points
                </div>

                <form method="POST" action="{{ route('fidelite.recompenses.destroy', $recompense) }}" class="mt-4" onsubmit="return confirm('Supprimer cette récompense ?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="loyalty-secondary text-[#b34c3e]">
                        <i data-lucide="trash-2" class="mr-1 inline h-3.5 w-3.5"></i>
                        Supprimer
                    </button>
                </form>
            </article>
            @empty
            <div class="loyalty-panel p-5 text-center text-[11px] text-muted md:col-span-3">
                Aucune récompense enregistrée pour le moment.
            </div>
            @endforelse
        </section>

    </div>

    {{-- HISTORIQUE --}}
    <div id="tab-history" class="loyalty-tab-content space-y-4" hidden>

        <section class="loyalty-panel overflow-hidden">
            <div class="p-4">
                <h3 class="text-[13px] font-semibold">Historique des opérations</h3>
                <p class="mt-1 text-[10px] text-muted">
                    Les opérations restent traçables, y compris les corrections et annulations.
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="loyalty-table w-full">
                    <thead>
                        <tr>
                            <th>DATE</th>
                            <th>CLIENT</th>
                            <th>OPÉRATION</th>
                            <th>RÉFÉRENCE</th>
                            <th>POINTS</th>
                            <th>STATUT</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($mouvements as $mouvement)
                        <tr>
                            <td>{{ $mouvement->created_at?->format('d/m/Y H:i') ?? '—' }}</td>

                            <td class="font-semibold">
                                {{ $mouvement->client?->nomComplet ?? 'Client indisponible' }}
                            </td>

                            <td>
                                @if ($mouvement->type->value === 'gain')
                                Attribution de points
                                @elseif ($mouvement->type->value === 'utilisation')
                                Utilisation de points
                                @else
                                Correction de points
                                @endif

                                @if ($mouvement->motif)
                                <div class="mt-1 text-[10px] text-muted">
                                    {{ $mouvement->motif }}
                                </div>
                                @endif
                            </td>

                            <td>
                                {{ $mouvement->paiement?->reference ?? ($mouvement->paiement_id ? 'Paiement #' . $mouvement->paiement_id : '—') }}
                            </td>

                            <td class="font-semibold {{ $mouvement->points >= 0 ? 'text-[#34734b]' : 'text-red-600' }}">
                                {{ $mouvement->points > 0 ? '+' : '' }}{{ number_format($mouvement->points, 0, ',', ' ') }}
                            </td>

                            <td>
                                @if ($mouvement->mouvement_origine_id)
                                <span class="loyalty-badge loyalty-muted">Correction liée</span>
                                @else
                                <span class="loyalty-badge loyalty-positive">Enregistré</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-5 text-center text-muted">
                                Aucune opération de fidélité enregistrée.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="loyalty-panel p-4">
            <div class="flex items-start gap-3">
                <div class="loyalty-icon"><i data-lucide="shield-check"></i></div>
                <div>
                    <h3 class="text-[12px] font-semibold">Protection de l'historique</h3>
                    <p class="mt-1 text-[10px] leading-5 text-muted">
                        Aucun point ne doit être accordé pour un rendez-vous annulé ou non payé. Les attributions doivent être liées à un paiement confirmé et ne pas être dupliquées. Les corrections doivent conserver l'opération d'origine et indiquer leur auteur, leur date et leur motif.
                    </p>
                </div>
            </div>
        </section>

    </div>

</div>
@endsection

@push('scripts')
@php
    // Compatible avec ->paginate() comme avec ->get()
    $listeClients = collect(
        $clients instanceof \Illuminate\Pagination\AbstractPaginator
            ? $clients->items()
            : $clients
    );

    $clientsJavascript = $listeClients->mapWithKeys(function ($client) use ($recompenses) {
        $mouvementsClient = $client->mouvementsPoints->sortByDesc('created_at');

        $pointsUtilises = abs(
            $mouvementsClient
                ->filter(fn ($m) => $m->type->value === 'utilisation')
                ->sum('points')
        );

        $nbRecompenses = $recompenses
            ->where('is_active', true)
            ->where('seuil_points', '<=', $client->solde_points)
            ->count();

        $historique = $mouvementsClient->map(function ($mouvement) {
            $operation = match ($mouvement->type->value) {
                'gain' => 'Points gagnés',
                'utilisation' => 'Récompense utilisée',
                default => 'Correction de points',
            };

            return [
                $mouvement->created_at?->format('d/m/Y') ?? '—',
                $operation,
                $mouvement->paiement?->reference
                    ?? ($mouvement->paiement_id ? 'Paiement #' . $mouvement->paiement_id : 'Mouvement #' . $mouvement->id),
                ($mouvement->points > 0 ? '+' : '') . number_format($mouvement->points, 0, ',', ' '),
            ];
        })->values();

        return [
            (string) $client->id => [
                'name' => $client->nomComplet,
                'balance' => number_format($client->solde_points, 0, ',', ' ') . ' pts',
                'earned' => number_format($client->points_gagnes, 0, ',', ' ') . ' pts',
                'used' => number_format($pointsUtilises, 0, ',', ' ') . ' pts',
                'rewards' => (string) $nbRecompenses,
                'history' => $historique,
            ],
        ];
    });
@endphp
<script>
    document.addEventListener('DOMContentLoaded', function () {

        const tabs = document.querySelectorAll('.loyalty-tab');
        const contents = document.querySelectorAll('.loyalty-tab-content');

        function ouvrirOnglet(nom) {
            tabs.forEach(tab => {
                const actif = tab.dataset.tab === nom;
                tab.classList.toggle('active', actif);
                tab.setAttribute('aria-selected', actif ? 'true' : 'false');
            });

            contents.forEach(section => {
                section.hidden = section.id !== 'tab-' + nom;
            });
        }

        tabs.forEach(tab => {
            tab.addEventListener('click', () => ouvrirOnglet(tab.dataset.tab));
        });

        document.querySelectorAll('[data-open-tab]').forEach(button => {
            button.addEventListener('click', () => ouvrirOnglet(button.dataset.openTab));
        });

        // Ouvrir l'onglet indiqué dans l'URL (#rules, #rewards...) après une action
        const ongletUrl = window.location.hash.replace('#', '');
        if (['overview', 'clients', 'rules', 'rewards', 'history'].includes(ongletUrl)) {
            ouvrirOnglet(ongletUrl);
        }

        // ----- Règles : modifier -----
        const regleForm = document.getElementById('regle-form');
        const regleMethod = document.getElementById('regle-method');
        const regleActive = document.getElementById('regle-active');

        function modeCreationRegle() {
            regleForm.action = regleForm.dataset.storeUrl;
            regleMethod.disabled = true;
            regleActive.disabled = true;
            document.getElementById('regle-active-wrap').hidden = true;
            document.getElementById('regle-mode').hidden = true;
            document.getElementById('regle-submit-label').textContent = 'Enregistrer la règle';
        }

        function modeEditionRegle(d) {
            regleForm.action = d.url;
            regleMethod.disabled = false;
            regleForm.elements['libelle'].value = d.libelle;
            regleForm.elements['montant_par_tranche'].value = d.montant_par_tranche;
            regleForm.elements['points_par_tranche'].value = d.points_par_tranche;
            regleForm.elements['date_debut'].value = d.date_debut;
            regleForm.elements['date_fin'].value = d.date_fin || '';
            regleActive.disabled = false;
            regleActive.checked = d.is_active;
            document.getElementById('regle-active-wrap').hidden = false;
            document.getElementById('regle-mode').hidden = false;
            document.getElementById('regle-submit-label').textContent = 'Mettre à jour la règle';
            ouvrirOnglet('rules');
            regleForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        regleForm.addEventListener('reset', () => setTimeout(modeCreationRegle, 0));

        document.querySelectorAll('[data-edit-regle]').forEach(button => {
            button.addEventListener('click', () => modeEditionRegle(JSON.parse(button.dataset.editRegle)));
        });

        const clients = @js($clientsJavascript);
        const detail = document.getElementById('client-detail');

        document.querySelectorAll('[data-client-detail]').forEach(button => {
            button.addEventListener('click', () => {
                const client = clients[button.dataset.clientDetail];
                if (!client) return;

                document.getElementById('detail-name').textContent = client.name;
                document.getElementById('detail-balance').textContent = client.balance;
                document.getElementById('detail-earned').textContent = client.earned;
                document.getElementById('detail-used').textContent = client.used;
                document.getElementById('detail-rewards').textContent = client.rewards;

                const tbody = document.getElementById('detail-history');
                tbody.replaceChildren();

                client.history.forEach(operation => {
                    const tr = document.createElement('tr');

                    operation.forEach((value, index) => {
                        const td = document.createElement('td');
                        td.textContent = value;
                        if (index === 3) {
                            td.classList.add(value.startsWith('+') ? 'text-[#34734b]' : 'text-[#b34c3e]', 'font-semibold');
                        }
                        tr.appendChild(td);
                    });

                    tbody.appendChild(tr);
                });

                detail.hidden = false;
                detail.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        });

        document.getElementById('close-client-detail').addEventListener('click', () => {
            detail.hidden = true;
        });

        document.getElementById('client-search').addEventListener('input', function () {
            const recherche = this.value.toLocaleLowerCase('fr');
            document.querySelectorAll('#client-list tr').forEach(row => {
                if (!row.dataset.client) return;
                row.hidden = !row.dataset.client.toLocaleLowerCase('fr').includes(recherche);
            });
        });

    });
</script>
@endpush