@extends('layouts.app')
@section('title', 'Stock')

@section('content')
@php
    $role = auth()->user()->role->name;
    $peutGerer = in_array($role, config('stock.roles_gestion'));
    $peutSortir = in_array($role, config('stock.roles_sortie'));
    $fmt = fn ($n) => number_format($n, 0, ',', ' ') . ' F';
    $qte = fn ($n) => rtrim(rtrim(number_format($n, 2, ',', ' '), '0'), ',');
@endphp

@if (session('success'))
    <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-[13px] text-emerald-700">{{ session('success') }}</div>
@endif

<div class="flex flex-wrap items-center justify-between gap-3">
    <p class="text-[13px] text-muted">Suivez vos produits, vos approvisionnements et vos sorties.</p>
    <div class="flex gap-2">
        <a href="{{ route('stock.mouvements.index') }}"
           class="rounded-lg border border-line bg-white px-3.5 py-2 text-[13px] font-medium text-[#716b70] hover:bg-paper">
            Historique des mouvements
        </a>
        @if ($peutGerer)
            <a href="{{ route('stock.produits.create') }}"
               class="rounded-lg bg-wine px-3.5 py-2 text-[13px] font-semibold text-white hover:bg-wine-dark">
                Ajouter un produit
            </a>
        @endif
    </div>
</div>

{{-- Indicateurs : cliquables pour filtrer --}}
<div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
    @foreach ([
        ['', 'Produits suivis', $stats['total'], 'text-[#292327]'],
        ['faible', 'Stocks faibles', $stats['faibles'], 'text-[#b77926]'],
        ['presque_epuise', 'Presque épuisés', $stats['presque_epuise'], 'text-orange-700'],
        ['epuise', 'Épuisés', $stats['epuise'], 'text-red-700'],
    ] as [$statut, $label, $valeur, $couleur])
        <a href="{{ route('stock.index', array_filter(['statut' => $statut])) }}"
           class="rounded-xl border bg-white p-4 shadow-soft {{ request('statut', '') === $statut ? 'border-wine' : 'border-line' }}">
            <div class="text-[12px] text-muted">{{ $label }}</div>
            <div class="mt-1 text-[26px] font-semibold {{ $couleur }}">{{ $valeur }}</div>
        </a>
    @endforeach
</div>

@if ($stats['faibles'] > 0 && !request('statut'))
    <div class="rounded-lg border border-[#f1d9ae] bg-[#fff8ec] px-4 py-2.5 text-[13px] text-[#8a5a1a]">
        {{ $stats['faibles'] }} produit(s) sont au niveau d’alerte ou en dessous, dont {{ $stats['presque_epuise'] }} presque épuisé(s) et {{ $stats['epuise'] }} épuisé(s).
    </div>
@endif

{{-- Filtres --}}
<form method="GET" class="flex flex-wrap gap-2">
    <input type="search" name="q" value="{{ request('q') }}" placeholder="Rechercher un produit ou un fournisseur"
           class="h-[36px] w-full rounded-lg border border-line bg-white px-3 text-[13px] outline-none focus:border-wine sm:w-[300px]">
    <select name="categorie" class="h-[36px] rounded-lg border border-line bg-white px-3 text-[13px]">
        <option value="">Toutes les catégories</option>
        @foreach ($categories as $c)
            <option value="{{ $c }}" @selected(request('categorie') === $c)>{{ $c }}</option>
        @endforeach
    </select>
    <select name="statut" class="h-[36px] rounded-lg border border-line bg-white px-3 text-[13px]">
        <option value="">Tous les statuts</option>
        <option value="faible" @selected(request('statut') === 'faible')>Stocks faibles</option>
        <option value="presque_epuise" @selected(request('statut') === 'presque_epuise')>Presque épuisés</option>
        <option value="epuise" @selected(request('statut') === 'epuise')>Épuisés</option>
    </select>
    <button class="h-[36px] rounded-lg border border-line bg-white px-4 text-[13px] font-medium hover:bg-paper">Filtrer</button>
</form>

{{-- Tableau --}}
<div class="overflow-x-auto rounded-xl border border-line bg-white shadow-soft">
    <table class="w-full min-w-[860px] text-left text-[13px]">
        <thead class="border-b border-line text-[12px] text-muted">
            <tr>
                <th class="px-4 py-3 font-medium">Produit</th>
                <th class="px-4 py-3 font-medium">Catégorie</th>
                <th class="px-4 py-3 text-right font-medium">Quantité</th>
                <th class="px-4 py-3 text-right font-medium">Seuil</th>
                <th class="px-4 py-3 text-right font-medium">Prix d’achat</th>
                <th class="px-4 py-3 text-right font-medium">Prix de vente</th>
                <th class="px-4 py-3 font-medium">Statut</th>
                <th class="px-4 py-3 text-right font-medium">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-line">
            @forelse ($produits as $p)
                @php $s = \App\Models\Produit::STATUTS[$p->statut]; @endphp
                <tr>
                    <td class="px-4 py-3">
                        <div class="font-medium">{{ $p->nom }}</div>
                        @if ($p->fournisseur)<div class="text-[12px] text-muted">{{ $p->fournisseur }}</div>@endif
                    </td>
                    <td class="px-4 py-3 text-[#716b70]">{{ $p->categorie ?? '—' }}</td>
                    <td class="px-4 py-3 text-right font-semibold">{{ $qte($p->quantite) }} <span class="font-normal text-muted">{{ $p->unite }}</span></td>
                    <td class="px-4 py-3 text-right text-[#716b70]">{{ $qte($p->seuil_alerte) }}</td>
                    <td class="px-4 py-3 text-right">{{ $fmt($p->prix_achat) }}</td>
                    <td class="px-4 py-3 text-right">{{ $p->prix_vente !== null ? $fmt($p->prix_vente) : '—' }}</td>
                    <td class="px-4 py-3"><span class="rounded-full px-2 py-0.5 text-[11px] font-medium {{ $s['classes'] }}">{{ $s['label'] }}</span></td>
                    <td class="px-4 py-3">
                        <div class="flex justify-end gap-1.5">
                            @if ($peutGerer)
                                <a href="{{ route('stock.entree.create', $p) }}" class="rounded-md border border-line px-2.5 py-1 text-[12px] hover:bg-paper">Approvisionner</a>
                            @endif
                            @if ($peutSortir)
                                <a href="{{ route('stock.sortie.create', $p) }}" class="rounded-md border border-line px-2.5 py-1 text-[12px] hover:bg-paper">Sortie</a>
                            @endif
                            @if ($peutGerer)
                                <a href="{{ route('stock.produits.edit', $p) }}" class="rounded-md border border-line px-2.5 py-1 text-[12px] text-wine hover:bg-[#f6e9ed]">Modifier</a>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="px-4 py-10 text-center text-muted">Aucun produit trouvé. Ajoutez votre premier produit pour commencer le suivi.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $produits->links() }}
@endsection