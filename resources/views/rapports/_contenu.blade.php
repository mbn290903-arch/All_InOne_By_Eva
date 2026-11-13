@php
    $f = fn ($n) => number_format($n, 0, ',', ' ');
    $t = $rapport['totaux'];
    $rdv = $rapport['rendezVous'];
    $nomGroupe = ['jour' => 'jour', 'semaine' => 'semaine', 'mois' => 'mois'][$groupe];
    $points = fn ($cle) => collect($rapport['serie'])
        ->map(fn ($s) => ['libelle' => $s['libelle'], 'court' => $s['court'], 'valeur' => $s[$cle]])
        ->values()->all();
    $totalDepCat = (int) $rapport['depensesCategories']->sum('total');
    $totalModes = (int) $rapport['modes']->sum('total');
@endphp

{{-- Repères du chiffre d'affaires --}}
<section>
    <div class="mb-2 text-[11px] font-bold tracking-[.12em] text-muted">CHIFFRE D'AFFAIRES · REPÈRES (indépendants du filtre)</div>
    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
        @foreach ([['Aujourd\'hui', $rapport['reperes']['jour']], ['Cette semaine', $rapport['reperes']['semaine']], ['Ce mois', $rapport['reperes']['mois']]] as [$titre, $valeur])
            <div class="rounded-xl border border-line bg-white p-4">
                <div class="text-[11px] text-muted">{{ $titre }}</div>
                <div class="mt-1 text-[20px] font-bold tracking-tight">{{ $f($valeur) }} <span class="text-[10px] font-medium text-muted">FCFA</span></div>
            </div>
        @endforeach
    </div>
</section>

{{-- Synthèse de la période --}}
<section>
    <div class="mb-2 text-[11px] font-bold tracking-[.12em] text-muted">SYNTHÈSE DE LA PÉRIODE</div>
    <div class="grid grid-cols-2 gap-3 xl:grid-cols-4">
        <div class="rounded-xl border border-line bg-white p-4">
            <div class="text-[11px] text-muted">Recettes encaissées</div>
            <div class="mt-1 text-[20px] font-bold">{{ $f($t['recettes']) }} <span class="text-[10px] font-medium text-muted">FCFA</span></div>
        </div>
        <div class="rounded-xl border border-line bg-white p-4">
            <div class="text-[11px] text-muted">Dépenses enregistrées</div>
            <div class="mt-1 text-[20px] font-bold">{{ $f($t['depenses']) }} <span class="text-[10px] font-medium text-muted">FCFA</span></div>
        </div>
        <div class="rounded-xl border border-[#e8c4cd] bg-[#fffafb] p-4">
            <div class="text-[11px] text-wine-dark">Résultat estimé</div>
            <div class="mt-1 text-[20px] font-bold {{ $t['resultat'] >= 0 ? 'text-wine-dark' : 'text-red-600' }}">
                {{ $f($t['resultat']) }} <span class="text-[10px] font-medium text-muted">FCFA</span>
            </div>
            <div class="mt-1 text-[10px] text-muted">Indicateur de gestion, pas un bénéfice comptable définitif.</div>
        </div>
        <div class="rounded-xl border border-line bg-white p-4">
            <div class="text-[11px] text-muted">Visites (rendez-vous terminés)</div>
            <div class="mt-1 text-[20px] font-bold">{{ $f($t['visites']) }}</div>
        </div>
        <div class="rounded-xl border border-line bg-white p-4">
            <div class="text-[11px] text-muted">Nouveaux clients</div>
            <div class="mt-1 text-[20px] font-bold">{{ $f($rapport['clients']['nouveaux']) }}</div>
        </div>
        <div class="rounded-xl border border-line bg-white p-4">
            <div class="text-[11px] text-muted">Clients récurrents</div>
            <div class="mt-1 text-[20px] font-bold">{{ $f($rapport['clients']['recurrents']) }}</div>
            <div class="mt-1 text-[10px] text-muted">Inscrits avant la période et revenus pendant.</div>
        </div>
        <div class="rounded-xl border border-line bg-white p-4">
            <div class="text-[11px] text-muted">Rendez-vous sur la période</div>
            <div class="mt-1 text-[20px] font-bold">{{ $f($rdv['total']) }}</div>
        </div>
        <div class="rounded-xl border border-line bg-white p-4">
            <div class="text-[11px] text-muted">Taux d'annulation</div>
            <div class="mt-1 text-[20px] font-bold">{{ $rdv['taux'] === null ? '—' : $rdv['taux'] . ' %' }}</div>
            <div class="mt-1 text-[10px] text-muted">{{ $rdv['annules'] }} annulé(s) sur {{ $rdv['total'] }}</div>
        </div>
    </div>
</section>

{{-- Graphiques --}}
<section class="grid grid-cols-1 gap-3 lg:grid-cols-2" style="break-inside: avoid">
    <div class="rounded-xl border border-line bg-white p-4">
        <h2 class="mb-3 text-[13px] font-semibold">Chiffre d'affaires par {{ $nomGroupe }} (FCFA)</h2>
        @include('rapports._barres', ['points' => $points('recettes'), 'couleur' => '#76243d'])
    </div>
    <div class="rounded-xl border border-line bg-white p-4">
        <h2 class="mb-3 text-[13px] font-semibold">Évolution du nombre de visites par {{ $nomGroupe }}</h2>
        @include('rapports._barres', ['points' => $points('visites'), 'couleur' => '#bd7188'])
    </div>
</section>

{{-- Évolution détaillée --}}
<section class="rounded-xl border border-line bg-white" style="break-inside: avoid">
    <div class="border-b border-line px-4 py-3 text-[13px] font-semibold">Évolution détaillée et résultat estimé</div>
    <div class="max-h-[360px] overflow-auto">
        <table class="w-full text-left text-[12px]">
            <thead class="sticky top-0 bg-white text-[10px] uppercase text-muted">
                <tr>
                    <th class="px-4 py-2">Période</th>
                    <th class="px-4 py-2 text-right">Recettes</th>
                    <th class="px-4 py-2 text-right">Dépenses</th>
                    <th class="px-4 py-2 text-right">Résultat estimé</th>
                    <th class="px-4 py-2 text-right">Visites</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rapport['serie'] as $l)
                    <tr class="border-t border-line">
                        <td class="px-4 py-2">{{ $l['libelle'] }}</td>
                        <td class="px-4 py-2 text-right">{{ $f($l['recettes']) }}</td>
                        <td class="px-4 py-2 text-right">{{ $f($l['depenses']) }}</td>
                        <td class="px-4 py-2 text-right font-medium {{ $l['resultat'] < 0 ? 'text-red-600' : '' }}">{{ $f($l['resultat']) }}</td>
                        <td class="px-4 py-2 text-right">{{ $l['visites'] }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="border-t border-line font-semibold">
                <tr>
                    <td class="px-4 py-2">Total</td>
                    <td class="px-4 py-2 text-right">{{ $f($t['recettes']) }}</td>
                    <td class="px-4 py-2 text-right">{{ $f($t['depenses']) }}</td>
                    <td class="px-4 py-2 text-right">{{ $f($t['resultat']) }}</td>
                    <td class="px-4 py-2 text-right">{{ $t['visites'] }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</section>

<section class="grid grid-cols-1 gap-3 lg:grid-cols-2">

    {{-- Dépenses par catégorie --}}
    <div class="rounded-xl border border-line bg-white" style="break-inside: avoid">
        <div class="border-b border-line px-4 py-3 text-[13px] font-semibold">Dépenses par catégorie</div>
        <table class="w-full text-left text-[12px]">
            <thead class="text-[10px] uppercase text-muted">
                <tr><th class="px-4 py-2">Catégorie</th><th class="px-4 py-2 text-right">Nb</th><th class="px-4 py-2 text-right">Total</th><th class="px-4 py-2 text-right">Part</th></tr>
            </thead>
            <tbody>
                @forelse ($rapport['depensesCategories'] as $l)
                    <tr class="border-t border-line">
                        <td class="px-4 py-2">{{ $l->libelle }}</td>
                        <td class="px-4 py-2 text-right">{{ $l->nombre }}</td>
                        <td class="px-4 py-2 text-right">{{ $f($l->total) }}</td>
                        <td class="px-4 py-2 text-right text-muted">{{ $totalDepCat > 0 ? round($l->total / $totalDepCat * 100) : 0 }} %</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-6 text-center text-muted">Aucune dépense sur cette période.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Modes de paiement --}}
    <div class="rounded-xl border border-line bg-white" style="break-inside: avoid">
        <div class="border-b border-line px-4 py-3 text-[13px] font-semibold">Répartition des modes de paiement</div>
        <table class="w-full text-left text-[12px]">
            <thead class="text-[10px] uppercase text-muted">
                <tr><th class="px-4 py-2">Mode</th><th class="px-4 py-2 text-right">Paiements</th><th class="px-4 py-2 text-right">Encaissé</th><th class="px-4 py-2 text-right">Part</th></tr>
            </thead>
            <tbody>
                @forelse ($rapport['modes'] as $l)
                    <tr class="border-t border-line">
                        <td class="px-4 py-2">{{ $l->libelle }}</td>
                        <td class="px-4 py-2 text-right">{{ $l->nombre }}</td>
                        <td class="px-4 py-2 text-right">{{ $f($l->total) }}</td>
                        <td class="px-4 py-2 text-right text-muted">{{ $totalModes > 0 ? round($l->total / $totalModes * 100) : 0 }} %</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-6 text-center text-muted">Aucun paiement sur cette période.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Prestations les plus demandées --}}
    <div class="rounded-xl border border-line bg-white" style="break-inside: avoid">
        <div class="border-b border-line px-4 py-3 text-[13px] font-semibold">Prestations les plus demandées</div>
        <table class="w-full text-left text-[12px]">
            <thead class="text-[10px] uppercase text-muted">
                <tr><th class="px-4 py-2">Prestation</th><th class="px-4 py-2 text-right">Nombre</th><th class="px-4 py-2 text-right">Montant</th></tr>
            </thead>
            <tbody>
                @forelse ($rapport['prestations'] as $l)
                    <tr class="border-t border-line">
                        <td class="px-4 py-2">{{ $l->libelle }}</td>
                        <td class="px-4 py-2 text-right">{{ $l->total }}</td>
                        <td class="px-4 py-2 text-right">{{ $f($l->montant) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="px-4 py-6 text-center text-muted">Aucune prestation terminée sur cette période.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Rendez-vous par statut --}}
    <div class="rounded-xl border border-line bg-white" style="break-inside: avoid">
        <div class="border-b border-line px-4 py-3 text-[13px] font-semibold">Rendez-vous par statut</div>
        <table class="w-full text-left text-[12px]">
            <thead class="text-[10px] uppercase text-muted">
                <tr><th class="px-4 py-2">Statut</th><th class="px-4 py-2 text-right">Nombre</th><th class="px-4 py-2 text-right">Part</th></tr>
            </thead>
            <tbody>
                @forelse ($rdv['parStatut'] as $l)
                    <tr class="border-t border-line">
                        <td class="px-4 py-2">{{ $l['libelle'] }}</td>
                        <td class="px-4 py-2 text-right">{{ $l['total'] }}</td>
                        <td class="px-4 py-2 text-right text-muted">{{ $rdv['total'] > 0 ? round($l['total'] / $rdv['total'] * 100) : 0 }} %</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="px-4 py-6 text-center text-muted">Aucun rendez-vous sur cette période.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

{{-- Performances des employés --}}
<section class="rounded-xl border border-line bg-white" style="break-inside: avoid">
    <div class="border-b border-line px-4 py-3 text-[13px] font-semibold">Performances des employés</div>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-[12px]">
            <thead class="text-[10px] uppercase text-muted">
                <tr>
                    <th class="px-4 py-2">Employé</th>
                    <th class="px-4 py-2 text-right">Rendez-vous</th>
                    <th class="px-4 py-2 text-right">Terminés</th>
                    <th class="px-4 py-2 text-right">Annulés</th>
                    <th class="px-4 py-2 text-right">Encaissé</th>
                    <th class="px-4 py-2 text-right">Commission estimée</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rapport['employes'] as $e)
                    <tr class="border-t border-line">
                        <td class="px-4 py-2">{{ $e['nom'] }}</td>
                        <td class="px-4 py-2 text-right">{{ $e['rdv'] }}</td>
                        <td class="px-4 py-2 text-right">{{ $e['termines'] }}</td>
                        <td class="px-4 py-2 text-right">{{ $e['annules'] }}</td>
                        <td class="px-4 py-2 text-right font-medium">{{ $f($e['encaisse']) }}</td>
                        <td class="px-4 py-2 text-right text-muted">
                            {{ $e['commission'] === null ? '—' : $f($e['commission']) . ' (' . (float) $e['taux'] . ' %)' }}
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-6 text-center text-muted">Aucune activité sur cette période.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <p class="border-t border-line px-4 py-2 text-[10px] text-muted">
        « Encaissé » = paiements valides reçus pour les rendez-vous de l'employé. Commission estimée = encaissé × taux de commission.
    </p>
</section>