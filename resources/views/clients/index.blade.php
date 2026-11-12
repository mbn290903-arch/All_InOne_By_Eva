@extends('layouts.app')
@section('title', 'Clients — All in One by Eva')
@section('breadcrumb', 'Clients')

@php
    $onglets = [
        'tous' => 'Tous',
        'actifs' => 'Actifs',
        'inactifs' => 'Inactifs',
        'anniversaire' => 'Anniversaire proche',
        'archives' => 'Archives',
    ];
    $url = fn($t) => route(
        'clients.index',
        array_filter(
            [
                'tab' => $t,
                'q' => $q,
                'tri' => $tri,
            ],
            fn($value) => $value !== null && $value !== '',
        ),
    );
    $urlExport = route(
        'clients.export',
        array_filter(
            [
                'tab' => $tab,
                'q' => $q,
                'tri' => $tri,
            ],
            fn($value) => $value !== null && $value !== '',
        ),
    );
    $statutStyle = ['actif' => 'bg-emerald-100 text-emerald-800', 'inactif' => 'bg-amber-100 text-amber-800'];
@endphp

@section('content')

    @push('styles')
        <style>
            /* Liens de pagination */
            nav[role="navigation"] a {
                color: #76243D !important;
                border-color: #EAE5E2 !important;
                background-color: #FFFFFF !important;
                border-radius: 7px !important;
                margin: 0 5px;
                transition: all .2s ease;
            }

            nav[role="navigation"] a:hover {
                background-color: #F6E9ED !important;
                color: #5D1830 !important;
                border-color: #76243D !important;
            }

            /* Page actuellement sélectionnée */
            nav[role="navigation"] span[aria-current="page"] span {
                background-color: #76243D !important;
                border-color: #76243D !important;
                color: #FFFFFF !important;
                font-weight: 600;
                border-radius: 7px !important;
            }

            /* Éléments désactivés */
            nav[role="navigation"] span.cursor-default {
                color: #AAA2A5 !important;
            }
        </style>
    @endpush

    <div class="mx-auto max-w-6xl">

        {{-- En-tête --}}
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">Clients</h1>
                <p class="mt-1 text-sm text-neutral-500">{{ number_format($stats['tous'], 0, ',', ' ') }}
                    fiche{{ $stats['tous'] > 1 ? 's' : '' }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                @can('export', \App\Models\Client::class)
                    <a href="{{ $urlExport }}" title="Exporte la liste actuellement affichée (filtres inclus)"
                        class="rounded-lg border border-neutral-300 bg-white px-4 py-2.5 text-sm font-medium hover:bg-neutral-50">Exporter
                        en CSV</a>
                @endcan
                <a href="{{ route('clients.create') }}"
                    class="inline-flex items-center gap-2 rounded-lg bg-[#76243D] px-4 py-2.5 text-sm font-semibold text-white shadow-md transition-all duration-200 hover:bg-[#5D1830] hover:shadow-lg">
                    <i data-lucide="user-plus" class="h-4 w-4"></i>
                    Ajouter un client
                </a>
            </div>
        </div>


        {{-- Cartes de synthèse --}}
        <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ([['Tous les clients', $stats['tous'], 'tous', '#7a1a40'], ['Actifs', $stats['actifs'], 'actifs', '#059669'], ['Inactifs', $stats['inactifs'], 'inactifs', '#d97706'], ['Anniversaires · 30 j', $stats['anniversaire'], 'anniversaire', '#db2777']] as [$titre, $nb, $t, $couleur])
                <div style="border-left: 4px solid {{ $couleur }}; box-shadow: -8px 6px 16px -8px {{ $couleur }}66;"
                    class="rounded-xl border border-neutral-200 bg-white p-5">
                    <p class="text-sm text-neutral-500">{{ $titre }}</p>
                    <p class="mt-2 text-3xl font-semibold">{{ number_format($nb, 0, ',', ' ') }}</p>
                </div>
            @endforeach
        </div>

        {{-- Doublons --}}
        @if ($doublons->count())
            <div class="mt-6 rounded-lg border border-sky-200 bg-sky-50 px-4 py-3 text-sm text-sky-900">
                <strong class="font-semibold">{{ $doublons->count() }} numéro{{ $doublons->count() > 1 ? 's' : '' }}
                    partagé{{ $doublons->count() > 1 ? 's' : '' }}</strong>
                par plusieurs fiches. Vérifiez avant de fusionner : repérez les fiches marquées « Doublon ? » dans la liste.
            </div>
        @endif

        {{-- Liste --}}
        <div class="mt-6 rounded-xl border border-neutral-200 bg-white">

            {{-- Onglets --}}
            <nav class="flex flex-wrap gap-6 border-b border-neutral-200 px-6 pt-4 text-sm">
                @foreach ($onglets as $t => $l)
                    <a href="{{ $url($t) }}"
                        class="-mb-px border-b-[3px] pb-3 transition-colors duration-200
    {{ $tab === $t ? 'border-[#76243D] font-semibold text-[#76243D]' : 'border-transparent text-neutral-600 hover:border-[#F6E9ED] hover:text-[#76243D]' }}">
                        {{ $l }} <span
                            class="ml-1 rounded-full bg-neutral-100 px-2 py-0.5 text-xs text-neutral-500">{{ $stats[$t] }}</span>
                    </a>
                @endforeach
            </nav>

            {{-- Tri (la recherche se fait avec la barre du haut) --}}
            <form method="GET" action="{{ route('clients.index') }}"
                class="flex flex-wrap items-center justify-between gap-3 px-6 py-4">
                <input type="hidden" name="tab" value="{{ $tab }}">
                <input type="hidden" name="q" value="{{ $q }}">
                <p class="text-sm text-neutral-500">
                    @if ($q !== '')
                        Résultats pour « {{ $q }} » ·
                        <a href="{{ route('clients.index', array_filter(['tab' => $tab, 'tri' => $tri])) }}"
                            class="underline hover:text-neutral-900">Effacer la recherche</a>
                    @endif
                </p>
                <label class="flex items-center gap-2 text-sm text-neutral-500">Trier par
                    <select name="tri" onchange="this.form.submit()"
                        class="rounded-lg border border-neutral-200 px-3 py-2 text-sm text-neutral-900">
                        <option value="recent" @selected($tri === 'recent')>Plus récents</option>
                        <option value="nom" @selected($tri === 'nom')>Nom (A-Z)</option>
                    </select>
                </label>
            </form>

            @if ($clients->count())
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1000px] text-left text-sm">
                        <thead class="border-y border-neutral-200 bg-neutral-50 text-xs text-neutral-500">
                            <tr>
                                <th class="px-6 py-3 font-medium">Client</th>
                                <th class="px-3 py-3 font-medium">Contact</th>
                                <th class="px-3 py-3 font-medium">Dernière visite</th>
                                <th class="px-3 py-3 font-medium">Visites</th>
                                <th class="px-3 py-3 font-medium">Fidélité</th>
                                <th class="px-3 py-3 font-medium">Marketing</th>
                                <th class="px-3 py-3 font-medium">Statut</th>
                                <th class="px-3 py-3 text-right font-medium">Total dépensé</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100">
                            @foreach ($clients as $c)
                                @php
                                    $statut =
                                        $c->statut instanceof \BackedEnum ? $c->statut->value : (string) $c->statut;
                                    $code = $c->code ?? 'CL-' . str_pad($c->id, 6, '0', STR_PAD_LEFT);
                                    $ini = mb_strtoupper(mb_substr($c->prenom, 0, 1) . mb_substr($c->nom, 0, 1));
                                    $tel = preg_replace(
                                        '/^(\d{2})(\d{3})(\d{2})(\d{2})$/',
                                        '$1 $2 $3 $4',
                                        $c->telephone,
                                    );
                                @endphp
                                <tr class="hover:bg-neutral-50">
                                    <td class="px-6 py-3">
                                        <div class="flex items-center gap-3">
                                            <span
                                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#F6E9ED] text-sm font-bold text-[#76243D] ring-1 ring-[#76243D]/10">
                                                {{ $ini }}
                                            </span>
                                            <div>
                                                <p class="font-medium"><a href="{{ route('clients.show', $c) }}"
                                                        class="hover:text-brand-700 hover:underline">{{ $c->prenom }}
                                                        {{ $c->nom }}</a>
                                                    @if ($c->archived_at)
                                                        <span
                                                            class="ml-1 rounded-full bg-neutral-200 px-2 py-0.5 text-[11px] font-medium text-neutral-700">Archivé</span>
                                                    @endif
                                                    @if ($doublons->contains($c->telephone))
                                                        <span
                                                            class="ml-1 rounded-full bg-sky-100 px-2 py-0.5 text-[11px] font-medium text-sky-800">Doublon
                                                            ?</span>
                                                    @endif
                                                </p>
                                                <p class="text-xs text-neutral-500">{{ $code }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-3">{{ $tel }}</td>
                                    <td class="whitespace-nowrap px-3 py-3 text-neutral-500">
                                        {{ $c->derniere_visite ? \Illuminate\Support\Carbon::parse($c->derniere_visite)->translatedFormat('j M Y') : '—' }}
                                    </td>
                                    <td class="px-3 py-3 text-neutral-500">{{ $c->visites ?? '—' }}</td>
                                    <td class="whitespace-nowrap px-3 py-3 text-neutral-500">
                                        {{ number_format($c->points ?? 0, 0, ',', ' ') }} pts</td>
                                    <td class="px-3 py-3">
                                        @if ($c->consent_promotions)
                                            <span title="Accepte les promotions"
                                                class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-emerald-100 font-bold text-emerald-700">
                                                ✓
                                            </span>
                                        @else
                                            <span title="N'accepte pas les promotions"
                                                class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-red-100 font-bold text-red-600">
                                                ✕
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3">
                                        <span
                                            class="rounded-full px-2.5 py-1 text-xs font-medium {{ $statutStyle[$statut] ?? 'bg-neutral-100 text-neutral-600' }}">{{ ucfirst($statut) }}</span>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-3 text-right text-neutral-500">
                                        {{ isset($c->total_depense) ? number_format($c->total_depense, 0, ',', ' ') . ' FCFA' : '—' }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-3 text-right">
                                        <div class="flex justify-end gap-2">
                                            @unless ($c->archived_at)
                                                <a href="{{ route('clients.edit', $c) }}"
                                                    class="rounded-lg border border-neutral-200 px-2.5 py-1.5 text-xs font-medium hover:bg-neutral-50">Modifier</a>
                                            @endunless
                                            <a href="https://wa.me/221{{ $c->whatsapp }}" target="_blank" rel="noopener"
                                                title="Écrire sur WhatsApp"
                                                class="rounded-lg border border-[#25D366] bg-[#25D366] px-2.5 py-1.5 text-xs font-medium text-white transition hover:border-[#128C7E] hover:bg-[#128C7E]">
                                                WhatsApp
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div
                    class="flex flex-wrap items-center justify-between gap-3 border-t border-neutral-200 px-6 py-4 text-sm text-neutral-500">
                    <p>{{ $clients->firstItem() }}–{{ $clients->lastItem() }} sur {{ $clients->total() }}
                        client{{ $clients->total() > 1 ? 's' : '' }}</p>
                    <div>{{ $clients->links() }}</div>
                </div>
            @else
                {{-- Aucun résultat --}}
                <div class="px-6 py-14 text-center">
                    <p class="font-semibold">Aucun client trouvé</p>
                    <p class="mt-1 text-sm text-neutral-500">
                        {{ $q !== '' ? "Aucun résultat pour « {$q} ». Vous pouvez créer la fiche directement." : 'Aucune fiche dans cette liste pour le moment.' }}
                    </p>
                    <div class="mt-5 flex justify-center gap-2">
                        @if ($q !== '' || $tab !== 'tous')
                            <a href="{{ route('clients.index') }}"
                                class="rounded-lg border border-neutral-300 px-4 py-2 text-sm font-medium hover:bg-neutral-50">Effacer</a>
                        @endif
                        <a href="{{ route('clients.create') }}"
                            class="inline-flex items-center gap-2 rounded-lg bg-[#76243D] px-4 py-2.5 text-sm font-semibold text-white shadow-md transition-all duration-200 hover:bg-[#5D1830] hover:shadow-lg">
                            <i data-lucide="user-plus" class="h-4 w-4"></i>
                            Ajouter un client
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Message de confirmation --}}
    @if (session('status'))
        <div role="status"
            class="fixed bottom-6 right-6 z-[9999] w-[calc(100%-3rem)] max-w-sm rounded-xl bg-[#76243D] px-5 py-4 text-sm text-white shadow-lg">
            <strong class="block font-semibold">{{ session('status')[0] }}</strong>
            <span class="text-neutral-300">{{ session('status')[1] }}</span>
        </div>
    @endif
@endsection
