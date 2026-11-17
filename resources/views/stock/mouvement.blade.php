@extends('layouts.app')
@section('title', $type === 'entree' ? 'Approvisionnement' : 'Sortie de stock')
@section('page-title', $type === 'entree' ? 'Enregistrer un approvisionnement' : 'Enregistrer une sortie')

@section('content')
@php
    $champ = 'mt-1 h-[38px] w-full rounded-lg border border-line bg-white px-3 text-[13px] outline-none focus:border-wine';
    $lab = 'text-[12px] font-medium text-[#716b70]';
    $qte = rtrim(rtrim(number_format($produit->quantite, 2, ',', ' '), '0'), ',');
@endphp

<form method="POST" action="{{ route("stock.$type.store", $produit) }}"
      class="max-w-2xl space-y-5 rounded-xl border border-line bg-white p-5 shadow-soft">
    @csrf

    <div class="rounded-lg bg-paper px-4 py-3">
        <div class="text-[15px] font-semibold">{{ $produit->nom }}</div>
        <div class="text-[12px] text-muted">En stock : {{ $qte }} {{ $produit->unite }} · {{ $produit->categorie }}</div>
    </div>

    @if ($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-2.5 text-[13px] text-red-700">
            @foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach
        </div>
    @endif

    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="{{ $lab }}" for="quantite">Quantité ({{ $produit->unite }})</label>
            <input id="quantite" type="number" step="0.01" min="0.01" name="quantite" value="{{ old('quantite') }}" required autofocus class="{{ $champ }}">
        </div>

        <div>
            <label class="{{ $lab }}" for="motif">Motif</label>
            <select id="motif" name="motif" required class="{{ $champ }}">
                @foreach (\App\Models\MouvementStock::MOTIFS[$type] as $cle => $label)
                    @continue($cle === 'stock_initial' || $cle === 'consommation_prestation')
                    <option value="{{ $cle }}" @selected(old('motif') === $cle)>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        @if ($type === 'entree')
            <div>
                <label class="{{ $lab }}" for="prix_achat">Nouveau prix d’achat unitaire (F)</label>
                <input id="prix_achat" type="number" step="1" min="0" name="prix_achat" value="{{ old('prix_achat', $produit->prix_achat) }}" class="{{ $champ }}">
            </div>
            <div>
                <label class="{{ $lab }}" for="fournisseur">Fournisseur</label>
                <input id="fournisseur" name="fournisseur" value="{{ old('fournisseur', $produit->fournisseur) }}" class="{{ $champ }}">
            </div>
        @endif

        <div class="sm:col-span-2">
            <label class="{{ $lab }}" for="note">Note <span class="font-normal text-muted">(obligatoire pour une perte, une casse ou une correction, 200 caractères max)</span></label>
            <textarea id="note" name="note" rows="2" maxlength="200" class="mt-1 w-full rounded-lg border border-line bg-white px-3 py-2 text-[13px] outline-none focus:border-wine">{{ old('note') }}</textarea>
        </div>
    </div>

    <div class="flex justify-end gap-2 border-t border-line pt-4">
        <a href="{{ route('stock.index') }}" class="rounded-lg border border-line px-4 py-2 text-[13px] text-[#716b70] hover:bg-paper">Annuler</a>
        <button class="rounded-lg bg-wine px-4 py-2 text-[13px] font-semibold text-white hover:bg-wine-dark">
            {{ $type === 'entree' ? 'Enregistrer l’approvisionnement' : 'Enregistrer la sortie' }}
        </button>
    </div>
</form>
@endsection