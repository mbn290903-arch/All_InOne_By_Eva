@extends('layouts.app')
@section('title', 'Rendez-vous')
@section('page-title', 'Détail du rendez-vous')

@section('content')
@php
    $ctrl = \App\Http\Controllers\ProfessionnelController::class;
    $st = $ctrl::STATUTS[$rdv->statut] ?? ['label' => $rdv->statut, 'classes' => 'bg-gray-100 text-gray-600'];
    $actions = $ctrl::TRANSITIONS[$rdv->statut] ?? [];
@endphp

@if (session('success'))
    <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-[13px] text-emerald-700">{{ session('success') }}</div>
@endif
@if ($errors->any())
    <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-2.5 text-[13px] text-red-700">{{ $errors->first() }}</div>
@endif

<a href="{{ route('professionnel.planning', ['semaine' => $rdv->debut->toDateString()]) }}" class="text-[13px] text-wine hover:underline">← Retour au planning</a>

<div class="grid max-w-4xl gap-4 lg:grid-cols-2">
    <div class="space-y-3 rounded-xl border border-line bg-white p-5 shadow-soft">
        <div class="flex items-center justify-between">
            <div class="text-[12px] font-semibold uppercase tracking-wide text-muted">Rendez-vous</div>
            <span class="rounded-full px-2.5 py-0.5 text-[11px] font-medium {{ $st['classes'] }}">{{ $st['label'] }}</span>
        </div>
        <div class="text-[18px] font-semibold">{{ ucfirst($rdv->debut->translatedFormat('l d F Y')) }}</div>
        <div class="text-[14px] text-[#716b70]">{{ $rdv->debut->format('H:i') }} – {{ $rdv->fin->format('H:i') }}</div>

        <div class="border-t border-line pt-3">
            <div class="mb-1 text-[12px] font-medium text-muted">Prestations</div>
            <ul class="space-y-1 text-[13px]">
                @forelse ($rdv->services as $s)
                    <li class="flex justify-between"><span>{{ $s->libelle }}</span><span class="text-muted">{{ $s->duree_minutes }} min</span></li>
                @empty
                    <li class="text-muted">Aucune prestation</li>
                @endforelse
            </ul>
        </div>

        @if ($rdv->commentaire)
            <div class="border-t border-line pt-3">
                <div class="mb-1 text-[12px] font-medium text-muted">Commentaire</div>
                <p class="text-[13px]">{{ $rdv->commentaire }}</p>
            </div>
        @endif

        @if ($actions)
            <div class="flex gap-2 border-t border-line pt-3">
                @foreach ($actions as $action)
                    <form method="POST" action="{{ route('professionnel.rendez-vous.statut', $rdv->id) }}">
                        @csrf @method('PATCH')
                        <input type="hidden" name="statut" value="{{ $action }}">
                        <button class="rounded-lg bg-wine px-4 py-2 text-[13px] font-semibold text-white hover:bg-wine-dark">{{ $ctrl::ACTIONS[$action] }}</button>
                    </form>
                @endforeach
            </div>
        @endif
    </div>

<div class="space-y-3 rounded-xl border border-line bg-white p-5 shadow-soft">
    <div class="text-[12px] font-semibold uppercase tracking-wide text-muted">
        Client(e)
    </div>

    <div class="text-[18px] font-semibold">
        {{ $rdv->prenom }} {{ $rdv->nom }}
    </div>
</div>
</div>
@endsection
