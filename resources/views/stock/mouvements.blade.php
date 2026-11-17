@extends('layouts.app')
@section('title', 'Historique du stock')
@section('page-title', 'Historique des mouvements')

@section('content')
@php $qte = fn ($n) => rtrim(rtrim(number_format($n, 2, ',', ' '), '0'), ','); @endphp

<div class="flex items-center justify-between">
    <a href="{{ route('stock.index') }}" class="text-[13px] text-wine hover:underline">Retour au stock</a>
</div>

<form method="GET" class="flex flex-wrap gap-2">
    <select name="produit_id" class="h-[36px] rounded-lg border border-line bg-white px-3 text-[13px]">
        <option value="">Tous les produits</option>
        @foreach ($produits as $p)
            <option value="{{ $p->id }}" @selected(request('produit_id') == $p->id)>{{ $p->nom }}</option>
        @endforeach
    </select>
    <select name="type" class="h-[36px] rounded-lg border border-line bg-white px-3 text-[13px]">
        <option value="">Entrées et sorties</option>
        <option value="entree" @selected(request('type') === 'entree')>Entrées</option>
        <option value="sortie" @selected(request('type') === 'sortie')>Sorties</option>
    </select>
    <input type="date" name="du" value="{{ request('du') }}" class="h-[36px] rounded-lg border border-line bg-white px-3 text-[13px]" aria-label="Du">
    <input type="date" name="au" value="{{ request('au') }}" class="h-[36px] rounded-lg border border-line bg-white px-3 text-[13px]" aria-label="Au">
    <button class="h-[36px] rounded-lg border border-line bg-white px-4 text-[13px] font-medium hover:bg-paper">Filtrer</button>
</form>

<div class="overflow-x-auto rounded-xl border border-line bg-white shadow-soft">
    <table class="w-full min-w-[820px] text-left text-[13px]">
        <thead class="border-b border-line text-[12px] text-muted">
            <tr>
                <th class="px-4 py-3 font-medium">Date</th>
                <th class="px-4 py-3 font-medium">Produit</th>
                <th class="px-4 py-3 font-medium">Mouvement</th>
                <th class="px-4 py-3 text-right font-medium">Quantité</th>
                <th class="px-4 py-3 text-right font-medium">Avant → après</th>
                <th class="px-4 py-3 font-medium">Par</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-line">
            @forelse ($mouvements as $m)
                <tr>
                    <td class="whitespace-nowrap px-4 py-3 text-[#716b70]">{{ $m->created_at->translatedFormat('d M Y, H:i') }}</td>
                    <td class="px-4 py-3 font-medium">{{ $m->produit->nom }}</td>
                    <td class="px-4 py-3">
                        <span class="rounded-full px-2 py-0.5 text-[11px] font-medium {{ $m->type === 'entree' ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' }}">
                            {{ $m->type === 'entree' ? 'Entrée' : 'Sortie' }}
                        </span>
                        <span class="ml-1 text-[#716b70]">{{ $m->motif }}</span>
                    </td>
                    <td class="px-4 py-3 text-right font-semibold {{ $m->type === 'entree' ? 'text-emerald-700' : 'text-red-700' }}">
                        {{ $m->type === 'entree' ? '+' : '−' }}{{ $qte($m->quantite) }} {{ $m->produit->unite }}
                    </td>
                    <td class="whitespace-nowrap px-4 py-3 text-right text-[#716b70]">{{ $qte($m->quantite_avant) }} → {{ $qte($m->stock_apres) }}</td>
                    <td class="px-4 py-3 text-[#716b70]">{{ $m->enregistreur?->name ?? 'Système' }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-4 py-10 text-center text-muted">Aucun mouvement pour ces critères.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $mouvements->links() }}
@endsection