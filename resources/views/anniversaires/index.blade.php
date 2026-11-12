@extends('layouts.app')
@section('title', 'Anniversaires — All in One by Eva')
@section('page-title', 'Anniversaires')

@php
    $onglets = [
        'aujourdhui' => "Aujourd'hui",
        'semaine'    => 'Cette semaine',
        'mois'       => 'Ce mois (' . now()->translatedFormat('F') . ')',
        'sans-date'  => 'Date de naissance manquante',
    ];
    $vides = [
        'aujourdhui' => "Aucun anniversaire aujourd'hui.",
        'semaine'    => 'Aucun anniversaire dans les 7 prochains jours.',
        'mois'       => 'Aucun anniversaire ce mois-ci.',
    ];
    $initiales = fn($c) => mb_strtoupper(mb_substr($c->prenom, 0, 1) . mb_substr($c->nom, 0, 1));
    $quand = fn($j) => $j === 0 ? "Aujourd'hui" : ($j > 0 ? "dans {$j} j" : 'il y a ' . abs($j) . ' j');
@endphp

@section('content')
<div class="mx-auto max-w-6xl space-y-5">

    {{-- Offre d'anniversaire --}}
    <section class="rounded-[11px] border border-line bg-white p-5">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="min-w-0 flex-1">
                <h2 class="text-[15px] font-semibold">Offre d'anniversaire</h2>
                <p class="mt-1 text-sm text-muted">
                    Texte libre (réduction, soin offert…), ajouté aux messages WhatsApp.
                    Il n'est envoyé qu'aux clientes qui ont accepté les promotions.
                </p>
            </div>
        </div>

        @if ($peutOffre)
            <form method="POST" action="{{ route('anniversaires.offre') }}" class="mt-4 flex flex-wrap gap-2">
                @csrf
                <input type="text" name="offre" maxlength="200" value="{{ old('offre', $offre) }}"
                       placeholder="Ex. un soin du visage offert pour toute prestation de plus de 20 000 FCFA"
                       class="min-w-64 flex-1 rounded-lg border border-neutral-200 px-3 py-2.5 text-sm focus:border-wine focus:outline-none focus:ring-2 focus:ring-wine/20">
                <button type="submit" class="rounded-lg bg-wine px-4 py-2.5 text-sm font-medium text-white hover:bg-wine-dark">Enregistrer l'offre</button>
            </form>
            @error('offre')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
            <p class="mt-2 text-xs text-muted">Laissez vide pour envoyer un simple message de vœux, sans offre.</p>
        @else
            <p class="mt-4 rounded-lg bg-neutral-50 px-4 py-3 text-sm">
                @if ($offre) <strong>Offre en cours :</strong> {{ $offre }}
                @else <span class="text-muted">Aucune offre définie pour le moment. Seule l'administratrice peut la modifier.</span>
                @endif
            </p>
        @endif
    </section>

    {{-- Liste --}}
    <section class="rounded-[11px] border border-line bg-white">

        <nav class="flex flex-wrap gap-6 border-b border-line px-6 pt-4 text-sm">
            @foreach ($onglets as $t => $l)
                <a href="{{ route('anniversaires.index', array_filter(['tab' => $t, 'q' => $q])) }}"
                   class="-mb-px border-b-2 pb-3 {{ $tab === $t ? 'border-wine font-medium text-wine-dark' : 'border-transparent text-neutral-600 hover:text-neutral-900' }}">
                    {{ $l }}
                    <span class="ml-1 rounded-full bg-neutral-100 px-2 py-0.5 text-xs text-neutral-500">{{ $compteurs[$t] }}</span>
                </a>
            @endforeach
        </nav>

        @if ($q !== '')
            <p class="border-b border-line px-6 py-3 text-sm text-muted">
                Résultats pour « {{ $q }} » ·
                <a href="{{ route('anniversaires.index', ['tab' => $tab]) }}" class="underline hover:text-neutral-900">Effacer la recherche</a>
            </p>
        @endif

        @if ($tab === 'sans-date')
            {{-- Clients sans date de naissance --}}
            @if ($sansDatePage->count())
                <p class="px-6 pt-4 text-sm text-muted">Ces fiches ne peuvent pas recevoir de message d'anniversaire. Complétez la date de naissance pour les inclure.</p>
                <div class="overflow-x-auto">
                    <table class="mt-3 w-full min-w-[560px] text-left text-sm">
                        <thead class="border-y border-line bg-neutral-50 text-xs text-neutral-500">
                            <tr><th class="px-6 py-3 font-medium">Client</th><th class="px-3 py-3 font-medium">Téléphone</th><th class="px-6 py-3"></th></tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100">
                            @foreach ($sansDatePage as $c)
                                <tr class="hover:bg-neutral-50">
                                    <td class="px-6 py-3">
                                        <div class="flex items-center gap-3">
                                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#f0dfe4] text-xs font-semibold text-wine">{{ $initiales($c) }}</span>
                                            <a href="{{ route('clients.show', $c) }}" class="font-medium hover:text-wine hover:underline">{{ $c->prenom }} {{ $c->nom }}</a>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3">{{ preg_replace('/^(\d{2})(\d{3})(\d{2})(\d{2})$/', '$1 $2 $3 $4', $c->telephone) }}</td>
                                    <td class="px-6 py-3 text-right">
                                        @can('update', $c)
                                            <a href="{{ route('clients.edit', $c) }}" class="rounded-lg border border-neutral-200 px-3 py-1.5 text-xs font-medium hover:bg-neutral-50">Compléter la fiche</a>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-line px-6 py-4 text-sm">{{ $sansDatePage->links() }}</div>
            @else
                <p class="px-6 py-14 text-center text-sm text-muted">{{ $q !== '' ? "Aucun résultat pour « {$q} »." : 'Toutes les fiches ont une date de naissance.' }}</p>
            @endif
        @else
            {{-- Anniversaires --}}
            @if ($lignes->count())
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[860px] text-left text-sm">
                        <thead class="border-y border-line bg-neutral-50 text-xs text-neutral-500">
                            <tr>
                                <th class="px-6 py-3 font-medium">Client</th>
                                <th class="px-3 py-3 font-medium">Anniversaire</th>
                                <th class="px-3 py-3 font-medium">Âge</th>
                                <th class="px-3 py-3 font-medium">Message</th>
                                <th class="px-3 py-3 font-medium">Suivi</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100">
                            @foreach ($lignes as $l)
                                @php $c = $l->client; @endphp
                                <tr class="hover:bg-neutral-50">
                                    <td class="px-6 py-3">
                                        <div class="flex items-center gap-3">
                                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#f0dfe4] text-xs font-semibold text-wine">{{ $initiales($c) }}</span>
                                            <a href="{{ route('clients.show', $c) }}" class="font-medium hover:text-wine hover:underline">{{ $c->prenom }} {{ $c->nom }}</a>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3">
                                        {{ $l->date->translatedFormat('j F') }}
                                        <span class="block text-xs {{ $l->jours === 0 ? 'font-semibold text-wine' : 'text-muted' }}">{{ $quand($l->jours) }}</span>
                                    </td>
                                    <td class="px-3 py-3 text-neutral-600">{{ $l->age }} ans</td>
                                    <td class="px-3 py-3 text-xs">
                                        @if ($l->avec_offre)
                                            <span class="rounded-full bg-emerald-100 px-2 py-0.5 font-medium text-emerald-800">Vœux + offre</span>
                                        @elseif ($offre && ! $c->consent_promotions)
                                            <span class="rounded-full bg-neutral-100 px-2 py-0.5 text-neutral-600" title="Pas de consentement aux promotions : l'offre n'est pas ajoutée">Vœux seuls</span>
                                        @else
                                            <span class="text-muted">Vœux seuls</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3">
                                        @if ($l->traite)
                                            <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-medium text-emerald-800">Traité {{ $l->annee }}</span>
                                        @else
                                            <span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-medium text-amber-800">À traiter</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3">
                                        <div class="flex flex-wrap justify-end gap-2">
                                            @if ($l->wa_url)
                                                <a href="{{ $l->wa_url }}" target="_blank" rel="noopener"
                                                   class="rounded-lg bg-emerald-700 px-3 py-1.5 text-xs font-medium text-white hover:bg-emerald-800">WhatsApp</a>
                                            @else
                                                <span class="rounded-lg border border-neutral-200 px-3 py-1.5 text-xs text-neutral-400" title="Aucun numéro">WhatsApp</span>
                                            @endif

                                            @if ($l->traite)
                                                <form method="POST" action="{{ route('anniversaires.annuler', $c) }}">
                                                    @csrf @method('DELETE')
                                                    <input type="hidden" name="annee" value="{{ $l->annee }}">
                                                    <button type="submit" class="rounded-lg border border-neutral-200 px-3 py-1.5 text-xs font-medium hover:bg-neutral-50">Annuler</button>
                                                </form>
                                            @else
                                                <form method="POST" action="{{ route('anniversaires.traiter', $c) }}">
                                                    @csrf
                                                    <input type="hidden" name="annee" value="{{ $l->annee }}">
                                                    <button type="submit" class="rounded-lg border border-neutral-300 px-3 py-1.5 text-xs font-medium hover:bg-neutral-50">Marquer comme traité</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="px-6 py-14 text-center text-sm text-muted">{{ $q !== '' ? "Aucun résultat pour « {$q} »." : $vides[$tab] }}</p>
            @endif
        @endif
    </section>
</div>

{{-- Message de confirmation --}}
@if (session('status'))
    <div role="status" class="fixed bottom-6 left-6 z-50 max-w-sm rounded-xl bg-neutral-900 px-5 py-4 text-sm text-white shadow-lg">
        <strong class="block font-semibold">{{ session('status')[0] }}</strong>
        <span class="text-neutral-300">{{ session('status')[1] }}</span>
    </div>
@endif
@endsection
