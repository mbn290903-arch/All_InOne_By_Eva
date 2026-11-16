@extends('layouts.app')
@section('title', 'Modes de paiement')
@section('page-title', 'Modes de paiement')

@section('content')
    @include('partials.alertes')

    <form method="POST" action="{{ route('caisse.modes.store') }}" class="flex max-w-md gap-2">
        @csrf
        <input type="text" name="libelle" required placeholder="Nouveau mode (ex : Free Money)"
            class="flex-1 rounded-lg border border-line px-3 py-2 text-sm">
        <button class="rounded-lg bg-wine px-4 py-2 text-sm text-white">Ajouter</button>
    </form>

    <div class="max-w-md divide-y divide-line rounded-xl border border-line bg-white">
        @foreach ($modes as $m)
            <div class="flex items-center justify-between px-4 py-3 text-sm">
                <span class="{{ $m->is_active ? '' : 'text-muted line-through' }}">{{ $m->libelle }}</span>
                <form method="POST" action="{{ route('caisse.modes.toggle', $m) }}">
                    @csrf
                    @method('PATCH')
                    <button class="text-xs text-wine hover:underline">{{ $m->is_active ? 'Désactiver' : 'Activer' }}</button>
                </form>
            </div>
        @endforeach
    </div>
@endsection