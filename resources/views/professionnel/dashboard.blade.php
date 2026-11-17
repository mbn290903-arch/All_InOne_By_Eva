@extends('layouts.app')
@section('title', 'Tableau de bord')

@section('content')
@if (session('success'))
    <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-[13px] text-emerald-700">{{ session('success') }}</div>
@endif
@if ($errors->any())
    <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-2.5 text-[13px] text-red-700">{{ $errors->first() }}</div>
@endif

@if (! $employeLie)
    <div class="rounded-xl border border-[#f1d9ae] bg-[#fff8ec] p-5 text-[13px] text-[#8a5a1a]">
        Votre compte n’est pas encore relié à une fiche employé. Demandez à l’administrateur de faire le lien pour voir votre planning.
    </div>
@else
    <div>
        <div class="text-[20px] font-medium">Bonjour {{ auth()->user()->name }}</div>
        <div class="text-[13px] text-muted">{{ now()->translatedFormat('l d F Y') }}</div>
    </div>

    <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
        @foreach ([
            ['Rendez-vous aujourd’hui', $stats['aujourdhui']],
            ['Restant à faire', $stats['a_faire']],
            ['Terminés aujourd’hui', $stats['termines']],
            ['Cette semaine', $stats['semaine']],
        ] as [$label, $valeur])
            <div class="rounded-xl border border-line bg-white p-4 shadow-soft">
                <div class="text-[12px] text-muted">{{ $label }}</div>
                <div class="mt-1 text-[26px] font-semibold">{{ $valeur }}</div>
            </div>
        @endforeach
    </div>

    @if ($prochain)
        <div>
            <h2 class="mb-2 text-[13px] font-semibold uppercase tracking-wide text-muted">Prochain rendez-vous</h2>
            @include('professionnel._carte-rdv', ['r' => $prochain])
        </div>
    @endif

    <div>
        <div class="mb-2 flex items-center justify-between">
            <h2 class="text-[13px] font-semibold uppercase tracking-wide text-muted">Aujourd’hui</h2>
            <a href="{{ route('professionnel.planning') }}" class="text-[13px] text-wine hover:underline">Voir mon planning</a>
        </div>

        <div class="space-y-2">
            @forelse ($rdvsJour as $r)
                @include('professionnel._carte-rdv', ['r' => $r])
            @empty
                <div class="rounded-xl border border-line bg-white p-8 text-center text-[13px] text-muted">Aucun rendez-vous aujourd’hui.</div>
            @endforelse
        </div>
    </div>
@endif
@endsection
