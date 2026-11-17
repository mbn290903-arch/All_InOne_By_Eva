@extends('layouts.app')
@section('title', $produit->exists ? 'Modifier un produit' : 'Nouveau produit')
@section('page-title', $produit->exists ? 'Modifier un produit' : 'Nouveau produit')

@section('content')
@php
    $champ = 'mt-1 h-[38px] w-full rounded-lg border border-line bg-white px-3 text-[13px] outline-none focus:border-wine';
    $lab = 'text-[12px] font-medium text-[#716b70]';
    $err = fn ($n) => $errors->first($n) ? '<p class="mt-1 text-[12px] text-red-600">'.e($errors->first($n)).'</p>' : '';
@endphp

<form method="POST" class="max-w-3xl space-y-5 rounded-xl border border-line bg-white p-5 shadow-soft"
      action="{{ $produit->exists ? route('stock.produits.update', $produit) : route('stock.produits.store') }}">
    @csrf
    @if ($produit->exists) @method('PUT') @endif

    <div class="grid gap-4 sm:grid-cols-2">
        <div class="sm:col-span-2">
            <label class="{{ $lab }}" for="nom">Nom du produit</label>
            <input id="nom" name="nom" value="{{ old('nom', $produit->nom) }}" required class="{{ $champ }}">
            {!! $err('nom') !!}
        </div>

        <div>
            <label class="{{ $lab }}" for="categorie">Catégorie</label>
            <input id="categorie" name="categorie" list="liste-categories" value="{{ old('categorie', $produit->categorie) }}" required class="{{ $champ }}">
            <datalist id="liste-categories">@foreach ($categories as $c)<option value="{{ $c }}">@endforeach</datalist>
            {!! $err('categorie') !!}
        </div>

        <div>
            <label class="{{ $lab }}" for="unite">Unité de mesure</label>
            <input id="unite" name="unite" list="liste-unites" value="{{ old('unite', $produit->unite) }}" placeholder="ml, g, pièce, flacon…" required class="{{ $champ }}">
            <datalist id="liste-unites">@foreach (['pièce','flacon','ml','l','g','kg','boîte','paquet'] as $u)<option value="{{ $u }}">@endforeach</datalist>
            {!! $err('unite') !!}
        </div>

        @unless ($produit->exists)
            <div>
                <label class="{{ $lab }}" for="quantite">Quantité en stock au départ</label>
                <input id="quantite" type="number" step="0.01" min="0" name="quantite" value="{{ old('quantite', 0) }}" required class="{{ $champ }}">
                {!! $err('quantite') !!}
            </div>
        @else
            <div>
                <div class="{{ $lab }}">Quantité disponible</div>
                <div class="mt-1 flex h-[38px] items-center rounded-lg bg-paper px-3 text-[13px] text-[#716b70]">
                    {{ rtrim(rtrim(number_format($produit->quantite, 2, ',', ' '), '0'), ',') }} {{ $produit->unite }} · modifiable via approvisionnement ou sortie
                </div>
            </div>
        @endunless

        <div>
            <label class="{{ $lab }}" for="seuil_alerte">Seuil d’alerte</label>
            <input id="seuil_alerte" type="number" step="0.01" min="0" name="seuil_alerte" value="{{ old('seuil_alerte', $produit->seuil_alerte) }}" required class="{{ $champ }}">
            {!! $err('seuil_alerte') !!}
        </div>

        <div>
            <label class="{{ $lab }}" for="prix_achat">Prix d’achat (F)</label>
            <input id="prix_achat" type="number" min="0" step="1" name="prix_achat" value="{{ old('prix_achat', $produit->prix_achat) }}" required class="{{ $champ }}">
            {!! $err('prix_achat') !!}
        </div>

        <div>
            <label class="{{ $lab }}" for="prix_vente">Prix de vente (F), facultatif</label>
            <input id="prix_vente" type="number" min="0" step="1" name="prix_vente" value="{{ old('prix_vente', $produit->prix_vente) }}" class="{{ $champ }}">
            {!! $err('prix_vente') !!}
        </div>

        <div>
            <label class="{{ $lab }}" for="fournisseur">Fournisseur, facultatif</label>
            <input id="fournisseur" name="fournisseur" value="{{ old('fournisseur', $produit->fournisseur) }}" class="{{ $champ }}">
            {!! $err('fournisseur') !!}
        </div>

        <div>
            <label class="{{ $lab }}" for="date_entree">Date d’entrée en stock</label>
            <input id="date_entree" type="date" name="date_entree" value="{{ old('date_entree', optional($produit->date_entree)->format('Y-m-d')) }}" required class="{{ $champ }}">
            {!! $err('date_entree') !!}
        </div>
    </div>


    <div class="flex justify-end gap-2 border-t border-line pt-4">
        <a href="{{ route('stock.index') }}" class="rounded-lg border border-line px-4 py-2 text-[13px] text-[#716b70] hover:bg-paper">Annuler</a>
        <button class="rounded-lg bg-wine px-4 py-2 text-[13px] font-semibold text-white hover:bg-wine-dark">
            {{ $produit->exists ? 'Enregistrer les modifications' : 'Ajouter le produit' }}
        </button>
    </div>
</form>
@endsection