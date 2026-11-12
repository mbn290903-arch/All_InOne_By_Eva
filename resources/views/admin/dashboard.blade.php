@extends('layouts.app')

@section('title', 'Tableau de bord — All in One')
@section('page-title', 'Tableau de bord')

@push('styles')
    <style>
        .panel {
            background: white;
            border: 1px solid #eae5e2;
            border-radius: 11px;
            box-shadow: 0 1px 3px rgba(40, 20, 25, .035);
        }

        .icon-tile {
            width: 29px;
            height: 29px;
            display: grid;
            place-items: center;
            border-radius: 8px;
            background: #f7ecef;
            color: #76243d;
            flex-shrink: 0;
        }

        .icon-tile svg {
            width: 15px;
            height: 15px;
        }

        .metric-label {
            color: #817a7e;
            font-size: 10px;
            line-height: 1.45;
        }

        .metric-value {
            color: #292327;
            font-size: 21px;
            line-height: 1.2;
            font-weight: 700;
            letter-spacing: -.04em;
        }

        .small-note {
            color: #8b8588;
            font-size: 14px;
            line-height: 1.5;
        }

        .section-label {
            color: #827a7f;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .12em;
        }

        .chart-grid {
            stroke: #eee9e7;
            stroke-width: 1;
        }

        .chart-text {
            fill: #aaa2a5;
            font-size: 12px;
        }
    </style>
@endpush

@section('content')

    @php
        // ── Graphique des recettes (dessiné côté serveur, aucune valeur fictive) ──
        $joursMois = $maintenant->daysInMonth;
        $x0 = 48;
        $largeurGraph = 584;
        $yBase = 150;
        $hauteurGraph = 132;

        $px = fn($jour) => round($x0 + (($jour - 1) / max(1, $joursMois - 1)) * $largeurGraph, 1);
        $py = fn($valeur) => round($yBase - ($valeur / $maxRecetteGraphique) * $hauteurGraph, 1);

        $pointsCourants = collect($recettesGraphique)->map(fn($p) => $px($p['jour']) . ' ' . $py($p['courant']));
        $pointsPrecedents = collect($recettesGraphique)->map(fn($p) => $px($p['jour']) . ' ' . $py($p['precedent']));
        $dernierPoint = collect($recettesGraphique)->last();

        $cheminCourant = 'M' . $pointsCourants->implode(' L');
        $cheminPrecedent = 'M' . $pointsPrecedents->implode(' L');
        $cheminZone = $cheminCourant . ' L' . $px($dernierPoint['jour']) . ' ' . $yBase . ' L' . $x0 . ' ' . $yBase . ' Z';

        $formatK = fn($v) => $v >= 1000
            ? rtrim(rtrim(number_format($v / 1000, 1, '.', ''), '0'), '.') . ' k'
            : (string) (int) $v;

        // ── Répartition des prestations (anneau) ──
        $palette = ['#76243d', '#bd7188', '#e5c9cf', '#cbb79e', '#f1e9e1', '#d9a5b3', '#a66b7e'];
        $cumul = 0;
        $segments = [];
        foreach ($repartitionPrestations as $i => $ligne) {
            $part = $totalPrestations > 0 ? $ligne->total / $totalPrestations * 100 : 0;
            $segments[] = $palette[$i % count($palette)] . ' ' . round($cumul, 2) . '% ' . round($cumul + $part, 2) . '%';
            $cumul += $part;
        }
        $degrade = $segments ? 'conic-gradient(' . implode(', ', $segments) . ')' : 'conic-gradient(#eee9e7 0 100%)';

        $couleursServices = ['#76243d', '#8d3c55', '#a66b7e', '#bd8c9b', '#d0aeb8'];
    @endphp

    {{-- Titre et actions --}}
    <section>
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h1 class="text-[19px] font-semibold tracking-tight">
                Bonjour {{ auth()->user()->name }}
                <span class="font-normal text-[#8b8588]">— voici votre journée</span>
            </h1>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('clients.create') }}"
                    class="flex items-center gap-2 rounded-lg border border-[#ded8d6] bg-white px-3 py-2 text-[12px] font-medium">
                    <i data-lucide="user-round-plus" class="h-3.5 w-3.5"></i> Nouveau client
                </a>
                <a href="{{ route('rendez-vous.create') }}"
                    class="flex items-center gap-2 rounded-lg bg-wine px-3 py-2 text-[12px] font-medium text-white">
                    <i data-lucide="calendar-plus" class="h-3.5 w-3.5"></i> Nouveau rendez-vous
                </a>
            </div>
        </div>
    </section>

    {{-- Activité --}}
    <section>
        <div class="mb-3 flex items-center gap-3"><span class="section-label">ACTIVITÉ</span>
            <div class="h-px flex-1 bg-line"></div>
        </div>
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 xl:grid-cols-6">
            <div class="panel min-h-[112px] p-4">
                <div class="mb-3 flex items-start justify-between">
                    <div class="metric-label text-[12px] font-medium">Rendez-vous du jour</div>
                    <div class="icon-tile"><i data-lucide="calendar-days"></i></div>
                </div>
                <div class="metric-value">{{ $total }}</div>
                <div class="small-note mt-2">{{ $confirmes }} confirmés · <span
                        class="text-[#c17a27]">{{ $enAttente }} en attente</span></div>
            </div>
            <div class="panel min-h-[112px] p-4">
                <div class="mb-3 flex items-start justify-between">
                    <div class="metric-label text-[12px] font-medium">Prestations terminées</div>
                    <div class="icon-tile"><i data-lucide="circle-check"></i></div>
                </div>
                <div class="metric-value">{{ $termines }} <span class="text-[12px] font-normal text-muted">/
                        {{ $total }}</span></div>
                <div class="mt-3 h-[5px] overflow-hidden rounded-full bg-[#eee9e7]">
                    <div class="h-full rounded-full bg-wine" style="width: {{ $progression }}%"></div>
                </div>
            </div>
            <div class="panel min-h-[112px] p-4">
                <div class="mb-3 flex items-start justify-between">
                    <div class="metric-label text-[12px] font-medium">RDV à venir · 7 j</div>
                    <div class="icon-tile"><i data-lucide="calendar-clock"></i></div>
                </div>
                <div class="metric-value">{{ $aVenir }}</div>
                <div class="small-note mt-2">
                    @if ($evolution === null)
                        Pas de comparaison
                    @elseif ($evolution >= 0)
                        <span class="text-emerald-700">↗ +{{ $evolution }} %</span> vs 7 j précédents
                    @else
                        <span class="text-red-600">↘ {{ $evolution }} %</span> vs 7 j précédents
                    @endif
                </div>
            </div>
            <div class="panel min-h-[112px] p-4">
                <div class="mb-3 flex items-start justify-between">
                    <div class="metric-label text-[12px] font-medium">Total clients</div>
                    <div class="icon-tile"><i data-lucide="users"></i></div>
                </div>
                <div class="metric-value">{{ number_format($totalClients, 0, ',', ' ') }}</div>
                <div class="small-note mt-2">{{ number_format($clientsActifs, 0, ',', ' ') }} actifs</div>
            </div>
            <div class="panel min-h-[112px] p-4">
                <div class="mb-3 flex items-start justify-between">
                    <div class="metric-label text-[12px] font-medium">Nouveaux · {{ $moisCourant }}</div>
                    <div class="icon-tile"><i data-lucide="user-plus"></i></div>
                </div>
                <div class="metric-value">{{ $nouveauxClients }}</div>
                <div class="small-note mt-2">
                    @if ($evolutionClients > 0)
                        <span class="text-emerald-700">↗ +{{ $evolutionClients }}</span>
                    @elseif ($evolutionClients < 0)
                        <span class="text-red-600">↘ {{ $evolutionClients }}</span>
                    @else
                        Aucun changement
                    @endif
                    vs mois précédent
                </div>
            </div>
            <div class="panel min-h-[112px] p-4">
                <div class="mb-3 flex items-start justify-between">
                    <div class="metric-label text-[12px] font-medium">Clients à relancer</div>
                    <div class="icon-tile bg-[#fff2df] text-[#bf812f]"><i data-lucide="user-round-x"></i></div>
                </div>
                <div class="metric-value">{{ $clientsARecontacter }}</div>
                <div class="small-note mt-2 text-wine-dark">Sans visite depuis 60 j</div>
            </div>
        </div>
    </section>

    {{-- Indicateurs financiers --}}
    <section>
        <div class="mb-3 flex items-center gap-3">
            <span class="section-label">INDICATEURS FINANCIERS</span>
            <span class="rounded-full border border-line bg-white px-2 py-1 text-[9px] text-[#817a7e]">ⓘ Visible :
                administratrice</span>
            <div class="h-px flex-1 bg-line"></div>
        </div>
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
            <div class="panel p-4">
                <div class="mb-3 flex items-center justify-between"><span class="metric-label">Encaissé aujourd'hui</span>
                    <div class="icon-tile"><i data-lucide="banknote"></i></div>
                </div>
                <div class="metric-value">
                    {{ number_format($encaisseJour, 0, ',', ' ') }}
                    <span class="text-[10px] font-medium text-muted">FCFA</span>
                </div>
                <div class="small-note mt-2">
                    {{ $nombrePaiementsJour }} paiements · {{ $paiementsPartiels }} partiels
                </div>
            </div>
            <div class="panel p-4">
                <div class="mb-3 flex items-center justify-between"><span class="metric-label">Encaissé ·
                        {{ $moisCourant }}</span>
                    <div class="icon-tile"><i data-lucide="wallet"></i></div>
                </div>
                <div class="metric-value">
                    {{ number_format($encaisseMois, 0, ',', ' ') }}
                    <span class="text-[10px] font-medium text-muted">FCFA</span>
                </div>
                <div class="small-note mt-2">
                    @if ($evolutionRecettes === null)
                        Pas de comparaison disponible
                    @elseif ($evolutionRecettes >= 0)
                        <span class="text-emerald-700">↗ +{{ $evolutionRecettes }} %</span>
                        vs mois précédent à date
                    @else
                        <span class="text-red-600">↘ {{ $evolutionRecettes }} %</span>
                        vs mois précédent à date
                    @endif
                </div>
            </div>
            <div class="panel p-4">
                <div class="mb-3 flex items-center justify-between"><span class="metric-label">Dépenses ·
                        {{ $moisCourant }}</span>
                    <div class="icon-tile"><i data-lucide="receipt"></i></div>
                </div>
                <div class="metric-value">
                    {{ number_format($depensesMois, 0, ',', ' ') }}
                    <span class="text-[10px] font-medium text-muted">FCFA</span>
                </div>
                <div class="small-note mt-2">Dépenses enregistrées ce mois (hors annulées)</div>
            </div>
            <div class="panel border-[#e8c4cd] bg-[#fffafb] p-4">
                <div class="mb-3 flex items-center justify-between"><span class="metric-label text-wine-dark">Résultat
                        estimé · {{ $moisCourant }}</span>
                    <div class="icon-tile"><i data-lucide="scale"></i></div>
                </div>
                <div class="metric-value {{ $resultatMois >= 0 ? 'text-wine-dark' : 'text-red-600' }}">
                    {{ number_format($resultatMois, 0, ',', ' ') }}
                    <span class="text-[10px] font-medium text-muted">FCFA</span>
                </div>
                <div class="small-note mt-2">
                    Recettes encaissées − dépenses enregistrées.
                    Indicateur de gestion, pas un bénéfice comptable.
                </div>
            </div>
        </div>
    </section>

    {{-- Graphique et rendez-vous --}}
    <section class="grid grid-cols-1 gap-3 xl:grid-cols-[1.65fr_0.8fr]">
        <div class="panel overflow-hidden">
            <div class="flex items-center justify-between border-b border-line px-4 py-3">
                <div>
                    <h2 class="text-[11px] font-semibold">Évolution des recettes encaissées</h2>
                    <p class="text-[9px] text-muted">
                        {{ $maintenant->locale('fr')->translatedFormat('F Y') }} · par jour · FCFA
                    </p>
                </div>
                <div class="flex items-center gap-3 text-[9px]">
                    <span class="flex items-center gap-1.5 text-muted"><i class="h-0.5 w-3 bg-wine"></i>
                        {{ ucfirst($moisCourant) }}</span>
                    <span class="flex items-center gap-1.5 text-muted"><i
                            class="h-0.5 w-3 border-t border-dashed border-[#c8b29d]"></i>
                        {{ ucfirst($moisPrecedent) }}</span>
                    <a href="#" class="font-medium text-wine">Voir le rapport</a>
                </div>
            </div>
            <div class="px-4 pb-3 pt-4">
                <svg viewBox="0 0 650 190" class="h-[210px] w-full" role="img"
                    aria-label="Graphique des recettes encaissées">
                    <line x1="48" y1="18" x2="632" y2="18" class="chart-grid" />
                    <line x1="48" y1="62" x2="632" y2="62" class="chart-grid" />
                    <line x1="48" y1="106" x2="632" y2="106" class="chart-grid" />
                    <line x1="48" y1="150" x2="632" y2="150" class="chart-grid" />

                    <text x="4" y="21" class="chart-text">{{ $formatK($maxRecetteGraphique) }}</text>
                    <text x="4" y="65" class="chart-text">{{ $formatK($maxRecetteGraphique * 2 / 3) }}</text>
                    <text x="4" y="109" class="chart-text">{{ $formatK($maxRecetteGraphique / 3) }}</text>
                    <text x="27" y="153" class="chart-text">0</text>

                    <path d="{{ $cheminPrecedent }}" fill="none" stroke="#c8b29d" stroke-width="1.5"
                        stroke-dasharray="4 4" />
                    <path d="{{ $cheminZone }}" fill="#76243d" opacity=".06" />
                    <path d="{{ $cheminCourant }}" fill="none" stroke="#76243d" stroke-width="2" />
                    <circle cx="{{ $px($dernierPoint['jour']) }}" cy="{{ $py($dernierPoint['courant']) }}"
                        r="4" fill="white" stroke="#76243d" stroke-width="2" />

                    @foreach (array_unique([1, 7, 13, 19, 25, $joursMois]) as $j)
                        <text x="{{ $px($j) }}" y="170" class="chart-text">{{ $j }}</text>
                    @endforeach
                </svg>
            </div>
        </div>

        <div class="panel overflow-hidden">
            <div class="flex items-center justify-between border-b border-line px-4 py-3">
                <h2 class="text-[12px] font-semibold">Prochains rendez-vous</h2>
                <a href="{{ route('rendez-vous.index', ['vue' => 'jour']) }}"
                    class="text-[12px] font-medium text-wine">Planning →</a>
            </div>

            <div class="divide-y divide-line">
                @forelse ($prochains as $rdv)
                    @php
                        $minutes = (int) $rdv->debut->diffInMinutes($rdv->fin);
                        $duree =
                            $minutes >= 60
                                ? intdiv($minutes, 60) .
                                    ' h' .
                                    ($minutes % 60 ? ' ' . str_pad($minutes % 60, 2, '0', STR_PAD_LEFT) : '')
                                : $minutes . ' min';
                    @endphp

                    <div class="flex gap-3 px-4 py-3">
                        <div class="w-[48px] shrink-0">
                            <div class="text-[13px] font-semibold">{{ $rdv->debut->format('H:i') }}</div>
                            <div class="text-[12px] text-muted">{{ $duree }}</div>
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="truncate text-[13px] font-semibold">{{ $rdv->client->nomComplet }}</div>
                            <div class="truncate text-[11px] text-muted">
                                {{ $rdv->libellePrestations() }} · {{ $rdv->employe->nom_complet ?? 'Non attribué' }}
                                · <span
                                    class="{{ $rdv->zone === \App\Enums\ZoneSalon::Femmes ? 'text-[#bd7890]' : '' }}">{{ $rdv->zone->label() }}</span>
                            </div>
                        </div>

                        <span class="h-fit shrink-0 rounded-full px-2 py-1 text-[11px] {{ $rdv->statut->badge() }}">
                            ● {{ $rdv->statut->label() }}
                        </span>
                    </div>
                @empty
                    <div class="px-4 py-8 text-center text-[12px] text-muted">
                        Plus aucun rendez-vous aujourd'hui.
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- Répartition, services, clientèle --}}
    <section class="grid grid-cols-1 gap-3 lg:grid-cols-3">
        <div class="panel overflow-hidden">
            <div class="flex items-center justify-between border-b border-line px-4 py-3">
                <h2 class="text-[12px] font-semibold">Répartition des prestations</h2>
                <span class="text-[12px] text-muted">{{ $moisCourant }} · {{ $totalPrestations }} prestations</span>
            </div>
            <div class="flex items-center gap-4 p-4">
                <div class="relative h-[118px] w-[118px] shrink-0 rounded-full" style="background: {{ $degrade }}">
                    <div class="absolute inset-[20px] grid place-content-center rounded-full bg-white text-center">
                        <strong class="text-[18px]">{{ $totalPrestations }}</strong>
                        <span class="text-[10px] text-muted">prestations</span>
                    </div>
                </div>
                <div class="min-w-0 flex-1 space-y-2 text-[12px]">
                    @forelse ($repartitionPrestations as $i => $ligne)
                        <div class="flex items-center gap-2">
                            <i class="h-2 w-2 shrink-0 rounded-sm"
                                style="background: {{ $palette[$i % count($palette)] }}"></i>
                            <span class="truncate">{{ $ligne->libelle }}</span>
                            <span class="ml-auto text-muted">{{ $ligne->total }}</span>
                        </div>
                    @empty
                        <div class="text-muted">Aucune prestation terminée ce mois-ci.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="panel overflow-hidden">
            <div class="flex items-center justify-between border-b border-line px-4 py-3">
                <h2 class="text-[13px] font-semibold">Services les plus demandés</h2>
                <span class="text-[9px] text-muted">{{ $moisCourant }}</span>
            </div>
            <div class="space-y-3 p-4">
                @forelse ($servicesPopulaires as $i => $service)
                    <div>
                        <div class="mb-1 flex justify-between text-[12px]">
                            <span class="truncate">{{ $service->libelle }}</span>
                            <span class="text-muted">{{ $service->total }}</span>
                        </div>
                        <div class="h-[6px] rounded-full"
                            style="width: {{ round($service->total / $maxService * 100) }}%; background: {{ $couleursServices[$i % count($couleursServices)] }}">
                        </div>
                    </div>
                @empty
                    <div class="py-6 text-center text-[12px] text-muted">Aucune prestation terminée ce mois-ci.</div>
                @endforelse
            </div>
        </div>

        <div class="panel overflow-hidden">
            <div class="flex items-center justify-between border-b border-line px-4 py-3">
                <h2 class="text-[13px] font-semibold">Évolution de la clientèle</h2>
                <span class="text-[11px] text-muted">6 mois</span>
            </div>
            <div class="p-4">
                <div class="flex h-[125px] items-end justify-around gap-3 border-b border-line px-2">
                    @foreach ($evolutionClientele as $colonne)
                        <div class="flex h-full flex-1 flex-col items-center justify-end gap-1">
                            <div class="flex h-[110px] items-end gap-1">
                                <div class="w-[15px] rounded-t bg-[#ead4da]"
                                    style="height: {{ round($colonne['recurrents'] / $maxClientele * 105) }}px"
                                    title="{{ $colonne['recurrents'] }} récurrents"></div>
                                <div class="w-[15px] rounded-t bg-wine"
                                    style="height: {{ round($colonne['nouveaux'] / $maxClientele * 105) }}px"
                                    title="{{ $colonne['nouveaux'] }} nouveaux"></div>
                            </div>
                            <span class="text-[8px] text-muted">{{ $colonne['mois'] }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 flex gap-4 text-[12px] text-muted">
                    <span class="flex items-center gap-1"><i class="h-2 w-2 rounded-sm bg-[#ead4da]"></i> Clients
                        récurrents</span>
                    <span class="flex items-center gap-1"><i class="h-2 w-2 rounded-sm bg-wine"></i> Nouveaux
                        clients</span>
                </div>
            </div>
        </div>
    </section>

    {{-- Alertes et activité récente --}}
    <section class="grid grid-cols-1 gap-3 xl:grid-cols-[1.65fr_0.8fr]">
        <div class="panel overflow-hidden">
            <div class="flex items-center justify-between border-b border-line px-4 py-3">
                <h2 class="text-[13px] font-semibold">Alertes opérationnelles</h2>
                @if ($nombreAlertes > 0)
                    <span class="rounded-full bg-[#fbe7e8] px-2 py-1 text-[12px] font-semibold text-[#b64e54]">
                        {{ $nombreAlertes }} à traiter
                    </span>
                @endif
            </div>
            <div class="divide-y divide-line">

                @if ($nonConfirmesDemain->isNotEmpty())
                    <div class="flex items-center gap-3 px-4 py-2.5">
                        <div class="icon-tile bg-[#fff1df] text-[#bd812f]"><i data-lucide="calendar-clock"></i></div>
                        <div class="min-w-0 flex-1">
                            <div class="text-[14px] font-semibold">
                                {{ $nonConfirmesDemain->count() }}
                                {{ $nonConfirmesDemain->count() > 1 ? 'rendez-vous de demain ne sont pas confirmés' : "rendez-vous de demain n'est pas confirmé" }}
                            </div>
                            <div class="small-note">
                                {{ $nonConfirmesDemain->take(3)->map(fn($r) => $r->client->nomComplet . ' ' . $r->debut->format('H:i'))->join(', ') }}
                                @if ($nonConfirmesDemain->count() > 3)
                                    , …
                                @endif
                            </div>
                        </div>
                        <a href="{{ route('rendez-vous.index', ['vue' => 'jour', 'date' => now()->addDay()->toDateString()]) }}"
                            class="rounded-lg border border-[#ded8d6] px-3 py-1.5 text-[11px]">Voir demain</a>
                    </div>
                @endif

                @if ($anniversaires->isNotEmpty())
                    @php
                        $anniversairesAujourdhui = $anniversaires->filter(fn($a) => $a['date']->isToday());
                        $premier = $anniversairesAujourdhui->first() ?? $anniversaires->first();
                        $agePremier = $premier['date']->year - \Carbon\CarbonImmutable::parse($premier['client']->date_naissance)->year;
                    @endphp
                    <div class="flex items-center gap-3 px-4 py-2.5">
                        <div class="icon-tile bg-[#fff1df] text-[#bd812f]"><i data-lucide="cake"></i></div>
                        <div class="min-w-0 flex-1">
                            <div class="text-[14px] font-semibold">
                                {{ $anniversaires->count() }}
                                {{ $anniversaires->count() > 1 ? 'anniversaires' : 'anniversaire' }} dans les 7 prochains jours
                            </div>
                            <div class="small-note">
                                @if ($anniversairesAujourdhui->isNotEmpty())
                                    dont {{ $anniversairesAujourdhui->count() }} aujourd'hui ·
                                @else
                                    Prochain :
                                @endif
                                {{ $premier['client']->nomComplet }} ({{ $agePremier }} ans)
                                @if (! $premier['date']->isToday())
                                    le {{ $premier['date']->format('d/m') }}
                                @endif
                            </div>
                        </div>
                        <a href="#" class="rounded-lg border border-[#ded8d6] px-4 py-1.5 text-[11px]">Voir</a>
                    </div>
                @endif

                @if ($produitsEnAlerte->isNotEmpty())
                    <div class="flex items-center gap-3 px-4 py-2.5">
                        <div class="icon-tile bg-[#fbe9e9] text-[#bd5555]"><i data-lucide="package-alert"></i></div>
                        <div class="min-w-0 flex-1">
                            <div class="text-[14px] font-semibold">
                                {{ $produitsEnAlerte->count() }}
                                {{ $produitsEnAlerte->count() > 1 ? 'produits sont sous le seuil d\'alerte' : 'produit est sous le seuil d\'alerte' }}
                            </div>
                            <div class="small-note">
                                {{ $produitsEnAlerte->take(2)->map(fn($p) => $p->nom . ' (' . (float) $p->quantite . ' ' . $p->unite . ' / seuil ' . (float) $p->seuil_alerte . ')')->join(' · ') }}
                                @if ($produitsEnAlerte->count() > 2)
                                    · …
                                @endif
                            </div>
                        </div>
                        <a href="#" class="rounded-lg border border-[#ded8d6] px-3 py-1.5 text-[11px]">Approvisionner</a>
                    </div>
                @endif

                @if ($nombreRendezVousImpayes > 0)
                    <div class="flex items-center gap-3 px-4 py-2.5">
                        <div class="icon-tile bg-[#fff1df] text-[#bd812f]"><i data-lucide="hand-coins"></i></div>
                        <div class="min-w-0 flex-1">
                            <div class="text-[14px] font-semibold">
                                {{ $nombreRendezVousImpayes }}
                                {{ $nombreRendezVousImpayes > 1 ? 'prestations ont' : 'prestation a' }} un reste à payer
                            </div>
                            <div class="small-note">Total restant : {{ number_format($totalImpayes, 0, ',', ' ') }} FCFA
                            </div>
                        </div>
                        <a href="{{ route('caisse.impayes') }}"
                            class="rounded-lg border border-[#ded8d6] px-3 py-1.5 text-[11px]">Voir les impayés</a>
                    </div>
                @endif

                @if ($doublonsWhatsapp > 0)
                    <div class="flex items-center gap-3 px-4 py-2.5">
                        <div class="icon-tile bg-[#eaf1f8] text-[#537ba3]"><i data-lucide="users"></i></div>
                        <div class="min-w-0 flex-1">
                            <div class="text-[14px] font-semibold">
                                {{ $doublonsWhatsapp }}
                                {{ $doublonsWhatsapp > 1 ? 'doublons potentiels détectés' : 'doublon potentiel détecté' }}
                            </div>
                            <div class="small-note">Même numéro WhatsApp sur plusieurs fiches clientes</div>
                        </div>
                        <a href="{{ route('clients.index') }}"
                            class="rounded-lg border border-[#ded8d6] px-4 py-1.5 text-[11px]">Vérifier</a>
                    </div>
                @endif

                @if ($nombreAlertes === 0)
                    <div class="px-4 py-8 text-center text-[12px] text-muted">Aucune alerte pour le moment.</div>
                @endif
            </div>
        </div>

        <div class="panel overflow-hidden">
            <div class="flex items-center justify-between border-b border-line px-4 py-3">
                <h2 class="text-[13px] font-semibold">Activité récente</h2>
            </div>
            <div class="relative space-y-3 px-4 py-4">
                @if ($activitesRecentes->isNotEmpty())
                    <div class="absolute bottom-5 left-[21px] top-5 w-px bg-[#ead6dc]"></div>
                @endif
                @forelse ($activitesRecentes as $activite)
                    <div class="relative flex gap-3">
                        <i class="z-10 mt-1 h-2 w-2 shrink-0 rounded-full border-2 border-[#e8cbd3] bg-white"></i>
                        <div class="min-w-0">
                            <div class="truncate text-[13px] font-semibold">{{ $activite['titre'] }}</div>
                            <div class="small-note truncate">
                                {{ $activite['detail'] }} ·
                                {{ $activite['date']->locale('fr')->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-6 text-center text-[12px] text-muted">Aucune activité récente.</div>
                @endforelse
            </div>
        </div>
    </section>

@endsection 