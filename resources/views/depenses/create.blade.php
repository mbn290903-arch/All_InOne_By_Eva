@extends('layouts.app')
@section('title', 'Nouvelle dépense')
@section('page-title', 'Nouvelle dépense')

@section('content')
    @include('partials.alertes')

    <form method="POST" action="{{ route('depenses.store') }}" enctype="multipart/form-data"
        class="max-w-xl space-y-4 rounded-xl border border-line bg-white p-5">
        @csrf

        <div>
            <label class="mb-1 block text-sm font-medium">Catégorie</label>
            <select name="categorie_depense_id" required class="w-full rounded-lg border border-line px-3 py-2 text-sm">
                <option value="">— Choisir —</option>
                @foreach ($categories as $c)
                    <option value="{{ $c->id }}" @selected(old('categorie_depense_id') == $c->id)>{{ $c->nom }}</option>
                @endforeach
            </select>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="mb-1 block text-sm font-medium">Montant (FCFA)</label>
                <input type="number" min="1" name="montant" value="{{ old('montant') }}" required
                    class="w-full rounded-lg border border-line px-3 py-2 text-sm">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">Date</label>
                <input type="date" name="date_depense" max="{{ today()->toDateString() }}"
                    value="{{ old('date_depense', today()->toDateString()) }}" required
                    class="w-full rounded-lg border border-line px-3 py-2 text-sm">
            </div>
        </div>

        <div>
            <label class="mb-1 block text-sm font-medium">Description</label>
            <input type="text" name="description" value="{{ old('description') }}" required maxlength="255"
                class="w-full rounded-lg border border-line px-3 py-2 text-sm">
        </div>

        <div>
            <label class="mb-1 block text-sm font-medium">Justificatif (facultatif, image ou PDF, 4 Mo max)</label>
            <input type="file" name="justificatif" accept=".jpg,.jpeg,.png,.pdf" class="w-full text-sm">
        </div>

        <div class="flex justify-end gap-2">
            <a href="{{ route('depenses.index') }}" class="rounded-lg border border-line px-4 py-2 text-sm">Retour</a>
            <button class="rounded-lg bg-wine px-4 py-2 text-sm text-white">Enregistrer</button>
        </div>
    </form>
@endsection