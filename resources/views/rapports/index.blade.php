@extends('layouts.app')
@section('title', 'Rapports')
@section('page-title', 'Rapports et statistiques')

@section('content')
    @include('partials.alertes')

    @php
        $aujourdhui = now();
        $presets = [
            'Aujourd\'hui' => ['du' => $aujourdhui->toDateString(), 'au' => $aujourdhui->toDateString(), 'groupe' => 'jour'],
            '7 jours' => ['du' => $aujourdhui->copy()->subDays(6)->toDateString(), 'au' => $aujourdhui->toDateString(), 'groupe' => 'jour'],
            'Ce mois' => ['du' => $aujourdhui->copy()->startOfMonth()->toDateString(), 'au' => $aujourdhui->toDateString(), 'groupe' => 'jour'],
            'Mois dernier' => ['du' => $aujourdhui->copy()->subMonthNoOverflow()->startOfMonth()->toDateString(), 'au' => $aujourdhui->copy()->subMonthNoOverflow()->endOfMonth()->toDateString(), 'groupe' => 'jour'],
            '3 mois' => ['du' => $aujourdhui->copy()->subMonthsNoOverflow(2)->startOfMonth()->toDateString(), 'au' => $aujourdhui->toDateString(), 'groupe' => 'semaine'],
            'Cette année' => ['du' => $aujourdhui->copy()->startOfYear()->toDateString(), 'au' => $aujourdhui->toDateString(), 'groupe' => 'mois'],
        ];
        $parametres = ['du' => $debut->toDateString(), 'au' => $fin->toDateString(), 'groupe' => $groupe];
    @endphp

    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h1 class="text-[20px] font-semibold tracking-tight">Rapports et statistiques</h1>
            <p class="text-[12px] text-muted">
                Du {{ $debut->format('d/m/Y') }} au {{ $fin->format('d/m/Y') }} ·
                chiffres calculés à partir des opérations enregistrées.
            </p>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('rapports.export', $parametres) }}"
                class="flex items-center gap-2 rounded-lg border border-line bg-white px-4 py-2 text-sm font-medium">
                <i data-lucide="file-spreadsheet" class="h-4 w-4"></i> Exporter en CSV
            </a>
            <a href="{{ route('rapports.imprimer', $parametres) }}" target="_blank"
                class="flex items-center gap-2 rounded-lg bg-wine px-4 py-2 text-sm font-medium text-white">
                <i data-lucide="file-text" class="h-4 w-4"></i> Exporter en PDF
            </a>
        </div>
    </div>

    <div class="space-y-3 rounded-xl border border-line bg-white p-4">
        <div class="flex flex-wrap gap-2">
            @foreach ($presets as $titre => $p)
                <a href="{{ route('rapports.index', $p) }}"
                    class="rounded-full border border-line px-3 py-1 text-[12px] hover:bg-[#f6e9ed]">{{ $titre }}</a>
            @endforeach
        </div>

        <form method="GET" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="mb-1 block text-xs text-muted">Du</label>
                <input type="date" name="du" value="{{ $debut->toDateString() }}" class="rounded-lg border border-line px-3 py-2 text-sm">
            </div>
            <div>
                <label class="mb-1 block text-xs text-muted">Au</label>
                <input type="date" name="au" value="{{ $fin->toDateString() }}" class="rounded-lg border border-line px-3 py-2 text-sm">
            </div>
            <div>
                <label class="mb-1 block text-xs text-muted">Regrouper par</label>
                <select name="groupe" class="rounded-lg border border-line px-3 py-2 text-sm">
                    <option value="jour" @selected($groupe === 'jour')>Jour</option>
                    <option value="semaine" @selected($groupe === 'semaine')>Semaine</option>
                    <option value="mois" @selected($groupe === 'mois')>Mois</option>
                </select>
            </div>
            <button class="rounded-lg bg-wine px-4 py-2 text-sm text-white">Appliquer</button>
        </form>
    </div>

    @include('rapports._contenu')
@endsection