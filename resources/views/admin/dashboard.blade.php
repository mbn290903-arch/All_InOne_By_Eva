@extends('layouts.app')

@section('title', 'Tableau de bord — All in One')
@section('page-title', 'Tableau de bord')

{{-- Config Tailwind, police et styles de ce dashboard (le layout reste inchangé) --}}
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

    {{-- Titre et actions --}}
    <section>
        <div
            class="mb-2 inline-flex items-center gap-1.5 rounded-full bg-[#fff1df] px-2.5 py-1 text-[9px] font-medium text-[#b87925]">
            <i data-lucide="flask-conical" class="h-3 w-3"></i> Données de démonstration
        </div>
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h1 class="text-[19px] font-semibold tracking-tight">Bonjour Eva <span class="font-normal text-[#8b8588]">— voici
                    votre journée</span></h1>
            <div class="flex flex-wrap items-center gap-2">
                <div class="flex rounded-lg border border-line bg-white p-0.5 text-[12px]">
                    <button class="rounded-md bg-[#f6f3f1] px-3 py-2 font-semibold">Aujourd'hui</button>
                    <button class="px-3 py-2 text-[#817a7e]">7 jours</button>
                    <button class="px-3 py-2 text-[#817a7e]">Ce mois</button>
                </div>
                <button
                    class="flex items-center gap-2 rounded-lg border border-[#ded8d6] bg-white px-3 py-2 text-[12px] font-medium"><i
                        data-lucide="user-round-plus" class="h-3.5 w-3.5"></i> Nouveau client</button>
                <a href="{{ route('rendez-vous.create') }}"
                    class="flex items-center gap-2 rounded-lg bg-wine px-3 py-2 text-[12px] font-medium text-white"><i
                        data-lucide="calendar-plus" class="h-3.5 w-3.5"></i> Nouveau rendez-vous</a>
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
                <div class="metric-value">1 284</div>
                <div class="small-note mt-2">1 102 actifs</div>
            </div>
            <div class="panel min-h-[112px] p-4">
                <div class="mb-3 flex items-start justify-between">
                    <div class="metric-label text-[12px] font-medium">Nouveaux · octobre</div>
                    <div class="icon-tile"><i data-lucide="user-plus"></i></div>
                </div>
                <div class="metric-value">37</div>
                <div class="small-note mt-2"><span class="text-emerald-700">↗ +5</span> vs septembre</div>
            </div>
            <div class="panel min-h-[112px] p-4">
                <div class="mb-3 flex items-start justify-between">
                    <div class="metric-label text-[12px] font-medium">Clients à relancer</div>
                    <div class="icon-tile bg-[#fff2df] text-[#bf812f]"><i data-lucide="user-round-x"></i></div>
                </div>
                <div class="metric-value">146</div>
                <div class="small-note mt-2 text-wine-dark">Sans visite depuis 60 j →</div>
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
                <div class="metric-value">286 500 <span class="text-[10px] font-medium text-muted">FCFA</span></div>
                <div class="small-note mt-2">14 paiements · 2 partiels</div>
            </div>
            <div class="panel p-4">
                <div class="mb-3 flex items-center justify-between"><span class="metric-label">Encaissé · octobre</span>
                    <div class="icon-tile"><i data-lucide="wallet"></i></div>
                </div>
                <div class="metric-value">4 862 500 <span class="text-[10px] font-medium text-muted">FCFA</span></div>
                <div class="small-note mt-2"><span class="text-red-600">↘ -4,2 %</span> vs septembre (à date)</div>
            </div>
            <div class="panel p-4">
                <div class="mb-3 flex items-center justify-between"><span class="metric-label">Dépenses · octobre</span>
                    <div class="icon-tile"><i data-lucide="receipt"></i></div>
                </div>
                <div class="metric-value">2 140 000 <span class="text-[10px] font-medium text-muted">FCFA</span></div>
                <div class="small-note mt-2">dont salaires &amp; commissions 1 250 000</div>
            </div>
            <div class="panel border-[#e8c4cd] bg-[#fffafb] p-4">
                <div class="mb-3 flex items-center justify-between"><span class="metric-label text-wine-dark">Résultat
                        estimé · octobre</span>
                    <div class="icon-tile"><i data-lucide="scale"></i></div>
                </div>
                <div class="metric-value text-wine-dark">2 722 500 <span
                        class="text-[10px] font-medium text-muted">FCFA</span></div>
                <div class="small-note mt-2">ⓘ Recettes encaissées − dépenses enregistrées. Indicateur de gestion, pas un
                    bénéfice comptable.</div>
            </div>
        </div>
    </section>

    {{-- Graphique et rendez-vous --}}
    <section class="grid grid-cols-1 gap-3 xl:grid-cols-[1.65fr_0.8fr]">
        <div class="panel overflow-hidden">
            <div class="flex items-center justify-between border-b border-line px-4 py-3">
                <div><h2 class="text-[11px] font-semibold">Évolution des recettes encaissées</h2><p class="text-[9px] text-muted">Octobre 2026 · par jour · FCFA</p></div>
                <div class="flex items-center gap-3 text-[9px]"><span class="flex items-center gap-1.5 text-muted"><i class="h-0.5 w-3 bg-wine"></i> Octobre</span><span class="flex items-center gap-1.5 text-muted"><i class="h-0.5 w-3 border-t border-dashed border-[#c8b29d]"></i> Septembre</span><a href="#" class="font-medium text-wine">Voir le rapport</a></div>
            </div>
            <div class="px-4 pb-3 pt-4">
                <svg viewBox="0 0 650 190" class="h-[210px] w-full" role="img" aria-label="Graphique des recettes encaissées en octobre">
                    <line x1="48" y1="18" x2="632" y2="18" class="chart-grid"/><line x1="48" y1="62" x2="632" y2="62" class="chart-grid"/><line x1="48" y1="106" x2="632" y2="106" class="chart-grid"/><line x1="48" y1="150" x2="632" y2="150" class="chart-grid"/>
                    <text x="8" y="21" class="chart-text">300 k</text><text x="8" y="65" class="chart-text">200 k</text><text x="8" y="109" class="chart-text">100 k</text><text x="27" y="153" class="chart-text">0</text>
                    <path d="M48 111 L70 89 L91 101 L112 150 L133 70 L154 53 L175 74 L196 85 L217 76 L238 104 L259 150 L280 68 L301 50 L322 73 L343 83 L364 65 L385 150 L406 67 L427 45 L448 66 L469 76 L490 63 L511 99 L532 150 L553 61 L574 48 L595 77 L616 88" fill="none" stroke="#c8b29d" stroke-width="1.5" stroke-dasharray="4 4"/>
                    <path d="M48 103 L70 82 L91 94 L112 150 L133 68 L154 48 L175 70 L196 82 L217 72 L238 99 L259 150 L280 63 L301 44 L322 68 L343 79 L364 60 L385 150 L406 62 L427 40 L448 61 L469 72 L490 57 L511 94 L532 150 L553 55 L574 42 L595 73 L616 83 L616 150 L48 150 Z" fill="#76243d" opacity=".06"/>
                    <path d="M48 103 L70 82 L91 94 L112 150 L133 68 L154 48 L175 70 L196 82 L217 72 L238 99 L259 150 L280 63 L301 44 L322 68 L343 79 L364 60 L385 150 L406 62 L427 40 L448 61 L469 72 L490 57 L511 94 L532 150 L553 55 L574 42 L595 73 L616 83" fill="none" stroke="#76243d" stroke-width="2"/>
                    <circle cx="616" cy="83" r="4" fill="white" stroke="#76243d" stroke-width="2"/>
                    <text x="48" y="170" class="chart-text">1</text><text x="154" y="170" class="chart-text">7</text><text x="280" y="170" class="chart-text">13</text><text x="406" y="170" class="chart-text">19</text><text x="532" y="170" class="chart-text">25</text><text x="611" y="170" class="chart-text">31</text>
                    <text x="88" y="185" class="chart-text">lun. fermé</text>
                </svg>
            </div>
        </div>

        <div class="panel overflow-hidden">
            <div class="flex items-center justify-between border-b border-line px-4 py-3">
                <h2 class="text-[12px] font-semibold">Prochains rendez-vous</h2>
                <a href="{{ route('rendez-vous.index', ['vue' => 'jour']) }}" class="text-[12px] font-medium text-wine">Planning →</a>
            </div>

            <div class="divide-y divide-line">
                @forelse ($prochains as $rdv)
                    @php
                        $minutes = (int) $rdv->debut->diffInMinutes($rdv->fin);
                        $duree = $minutes >= 60
                            ? intdiv($minutes, 60) . ' h' . ($minutes % 60 ? ' ' . str_pad($minutes % 60, 2, '0', STR_PAD_LEFT) : '')
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
                                · <span class="{{ $rdv->zone === \App\Enums\ZoneSalon::Femmes ? 'text-[#bd7890]' : '' }}">{{ $rdv->zone->label() }}</span>
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
                <h2 class="text-[12px] font-semibold">Répartition des prestations</h2><span
                    class="text-[12px] text-muted">octobre · 412 prestations</span>
            </div>
            <div class="flex items-center gap-4 p-4">
                <div class="relative h-[118px] w-[118px] shrink-0 rounded-full"
                    style="background:conic-gradient(#76243d 0 37.9%, #bd7188 37.9% 62%, #e5c9cf 62% 80%, #cbb79e 80% 92.1%, #f1e9e1 92.1% 100%)">
                    <div class="absolute inset-[20px] grid place-content-center rounded-full bg-white text-center"><strong
                            class="text-[18px]">412</strong><span class="text-[10px] text-muted">prestations</span></div>
                </div>
                <div class="space-y-2 text-[12px]">
                    <div class="flex items-center gap-2"><i class="h-2 w-2 rounded-sm bg-wine"></i><span>Coiffure &amp;
                            tresses</span><span class="ml-auto text-muted">156</span></div>
                    <div class="flex items-center gap-2"><i
                            class="h-2 w-2 rounded-sm bg-[#bd7188]"></i><span>Ongles</span><span
                            class="ml-auto text-muted">99</span></div>
                    <div class="flex items-center gap-2"><i class="h-2 w-2 rounded-sm bg-[#e5c9cf]"></i><span>Massages
                            &amp; corps</span><span class="ml-auto text-muted">74</span></div>
                    <div class="flex items-center gap-2"><i
                            class="h-2 w-2 rounded-sm bg-[#cbb79e]"></i><span>Visage</span><span
                            class="ml-auto text-muted">50</span></div>
                    <div class="flex items-center gap-2"><i
                            class="h-2 w-2 rounded-sm bg-[#f1e9e1]"></i><span>Forfaits</span><span
                            class="ml-auto text-muted">33</span></div>
                </div>
            </div>
        </div>

        <div class="panel overflow-hidden">
            <div class="flex items-center justify-between border-b border-line px-4 py-3">
                <h2 class="text-[13px] font-semibold">Services les plus demandés</h2><span
                    class="text-[9px] text-muted">octobre</span>
            </div>
            <div class="space-y-3 p-4">
                <div>
                    <div class="mb-1 flex justify-between text-[12px]"><span>Box braids</span><span
                            class="text-muted">48</span></div>
                    <div class="h-[6px] rounded-full bg-[#76243d]"></div>
                </div>
                <div>
                    <div class="mb-1 flex justify-between text-[12px]"><span>Manucure semi-permanent</span><span
                            class="text-muted">41</span></div>
                    <div class="h-[6px] w-[85%] rounded-full bg-[#8d3c55]"></div>
                </div>
                <div>
                    <div class="mb-1 flex justify-between text-[12px]"><span>Coupe homme + barbe</span><span
                            class="text-muted">37</span></div>
                    <div class="h-[6px] w-[78%] rounded-full bg-[#a66b7e]"></div>
                </div>
                <div>
                    <div class="mb-1 flex justify-between text-[12px]"><span>Massage relaxant 60 min</span><span
                            class="text-muted">29</span></div>
                    <div class="h-[6px] w-[62%] rounded-full bg-[#bd8c9b]"></div>
                </div>
                <div>
                    <div class="mb-1 flex justify-between text-[12px]"><span>Soin visage éclat</span><span
                            class="text-muted">24</span></div>
                    <div class="h-[6px] w-[52%] rounded-full bg-[#d0aeb8]"></div>
                </div>
            </div>
        </div>

        <div class="panel overflow-hidden">
            <div class="flex items-center justify-between border-b border-line px-4 py-3">
                <h2 class="text-[13px] font-semibold">Évolution de la clientèle</h2><span class="text-[11px] text-muted">6
                    mois</span>
            </div>
            <div class="p-4">
                <div class="flex h-[125px] items-end justify-around gap-3 border-b border-line px-2">
                    @foreach ([['Mai', 80, 35], ['Juin', 90, 45], ['Juil.', 95, 32], ['Août', 110, 55], ['Sept.', 120, 42], ['Oct.', 125, 55]] as $bar)
                        <div class="flex h-full flex-1 flex-col items-center justify-end gap-1">
                            <div class="flex h-[110px] items-end gap-1">
                                <div class="w-[15px] rounded-t bg-[#ead4da]" style="height: {{ $bar[1] }}px"></div>
                                <div class="w-[15px] rounded-t bg-wine" style="height: {{ $bar[2] }}px"></div>
                            </div>
                            <span class="text-[8px] text-muted">{{ $bar[0] }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 flex gap-4 text-[12px] text-muted"><span class="flex items-center gap-1"><i
                            class="h-2 w-2 rounded-sm bg-[#ead4da]"></i> Clients récurrents</span><span
                        class="flex items-center gap-1"><i class="h-2 w-2 rounded-sm bg-wine"></i> Nouveaux clients</span>
                </div>
            </div>
        </div>
    </section>

    {{-- Alertes et activité récente --}}
    <section class="grid grid-cols-1 gap-3 xl:grid-cols-[1.65fr_0.8fr]">
        <div class="panel overflow-hidden">
            <div class="flex items-center justify-between border-b border-line px-4 py-3">
                <h2 class="text-[13px] font-semibold">Alertes opérationnelles</h2><span
                    class="rounded-full bg-[#fbe7e8] px-2 py-1 text-[12px] font-semibold text-[#b64e54]">5 à traiter</span>
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
                                {{ $nonConfirmesDemain->take(3)->map(fn ($r) => $r->client->nomComplet . ' ' . $r->debut->format('H:i'))->join(', ') }}@if ($nonConfirmesDemain->count() > 3), …@endif
                            </div>
                        </div>
                        <a href="{{ route('rendez-vous.index', ['vue' => 'jour', 'date' => now()->addDay()->toDateString()]) }}"
                           class="rounded-lg border border-[#ded8d6] px-3 py-1.5 text-[11px]">Voir demain</a>
                    </div>
                @endif
                <div class="flex items-center gap-3 px-4 py-2.5">
                    <div class="icon-tile bg-[#fff1df] text-[#bd812f]"><i data-lucide="cake"></i></div>
                    <div class="min-w-0 flex-1">
                        <div class="text-[14px] font-semibold">4 anniversaires à traiter cette semaine</div>
                        <div class="small-note">dont 1 aujourd'hui · Aïssatou Cissé (32 ans)</div>
                    </div><button class="rounded-lg border border-[#ded8d6] px-4 py-1.5 text-[11px]">Voir</button>
                </div>
                <div class="flex items-center gap-3 px-4 py-2.5">
                    <div class="icon-tile bg-[#fbe9e9] text-[#bd5555]"><i data-lucide="package-alert"></i></div>
                    <div class="min-w-0 flex-1">
                        <div class="text-[14px] font-semibold">2 produits sous le seuil d'alerte</div>
                        <div class="small-note">Mèches X-pression noir 1B (4 paquets / seuil 10) · Vernis gel top coat (1 /
                            seuil 3)</div>
                    </div><button
                        class="rounded-lg border border-[#ded8d6] px-3 py-1.5 text-[11px]">Approvisionner</button>
                </div>
                <div class="flex items-center gap-3 px-4 py-2.5">
                    <div class="icon-tile bg-[#fff1df] text-[#bd812f]"><i data-lucide="hand-coins"></i></div>
                    <div class="min-w-0 flex-1">
                        <div class="text-[14px] font-semibold">3 prestations avec un reste à payer</div>
                        <div class="small-note">Total restant : 42 500 FCFA</div>
                    </div><button class="rounded-lg border border-[#ded8d6] px-3 py-1.5 text-[11px]">Ouvrir la
                        caisse</button>
                </div>
                <div class="flex items-center gap-3 px-4 py-2.5">
                    <div class="icon-tile bg-[#eaf1f8] text-[#537ba3]"><i data-lucide="users"></i></div>
                    <div class="min-w-0 flex-1">
                        <div class="text-[14px] font-semibold">2 doublons potentiels détectés</div>
                        <div class="small-note">Même numéro WhatsApp sur deux fiches clientes</div>
                    </div><button class="rounded-lg border border-[#ded8d6] px-4 py-1.5 text-[11px]">Vérifier</button>
                </div>
            </div>
        </div>

        <div class="panel overflow-hidden">
            <div class="flex items-center justify-between border-b border-line px-4 py-3">
                <h2 class="text-[13px] font-semibold">Activité récente</h2><a href="#"
                    class="text-[13px] font-medium text-wine">Tout voir</a>
            </div>
            <div class="relative space-y-3 px-4 py-4">
                <div class="absolute bottom-5 left-[21px] top-5 w-px bg-[#ead6dc]"></div>
                <div class="relative flex gap-3"><i
                        class="z-10 mt-1 h-2 w-2 rounded-full border-2 border-[#e8cbd3] bg-white"></i>
                    <div>
                        <div class="text-[13px]"><strong>Awa S.</strong> a encaissé <strong>25 000 FCFA</strong> (Wave)
                        </div>
                        <div class="small-note">il y a 4 min · Mariama Ba</div>
                    </div>
                </div>
                <div class="relative flex gap-3"><i
                        class="z-10 mt-1 h-2 w-2 rounded-full border-2 border-[#e8cbd3] bg-white"></i>
                    <div>
                        <div class="text-[13px]">RDV de <strong>Ousmane Ndiaye</strong> confirmé</div>
                        <div class="small-note">il y a 12 min</div>
                    </div>
                </div>
                <div class="relative flex gap-3"><i
                        class="z-10 mt-1 h-2 w-2 rounded-full border-2 border-[#e8cbd3] bg-white"></i>
                    <div>
                        <div class="text-[13px]"><strong>Fatou D.</strong> a terminé <strong>Tresses enfant</strong></div>
                        <div class="small-note">il y a 25 min · Ndiaye Fatou Sow (8 ans)</div>
                    </div>
                </div>
                <div class="relative flex gap-3"><i
                        class="z-10 mt-1 h-2 w-2 rounded-full border-2 border-[#e8cbd3] bg-white"></i>
                    <div>
                        <div class="text-[13px]">Nouvelle cliente <strong>Coumba Diouf</strong></div>
                        <div class="small-note">il y a 41 min · par Awa S.</div>
                    </div>
                </div>
                <div class="relative flex gap-3"><i
                        class="z-10 mt-1 h-2 w-2 rounded-full border-2 border-[#e8cbd3] bg-white"></i>
                    <div>
                        <div class="text-[13px]">Sortie de stock : <strong>2 × Huile de massage</strong></div>
                        <div class="small-note">il y a 1 h · Ndiaye S.</div>
                    </div>
                </div>
                <div class="relative flex gap-3"><i
                        class="z-10 mt-1 h-2 w-2 rounded-full border-2 border-[#e8cbd3] bg-white"></i>
                    <div>
                        <div class="text-[13px]">Dépense : <strong>Facture Senelec</strong> 86 400 FCFA</div>
                        <div class="small-note">09:12 · Eva N.</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
