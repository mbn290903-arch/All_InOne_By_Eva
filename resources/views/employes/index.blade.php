@extends('layouts.app')
@section('title', 'Employés')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3">
    @if ($voirPerformances)
        <form method="GET" class="flex items-center gap-2 text-[12px]">
            <input type="date" name="du" value="{{ $du->toDateString() }}" class="rounded-lg border border-line px-2 py-1.5">
            <span class="text-muted">→</span>
            <input type="date" name="au" value="{{ $au->toDateString() }}" class="rounded-lg border border-line px-2 py-1.5">
            <button class="rounded-lg border border-line px-3 py-1.5 hover:bg-paper">Filtrer</button>
        </form>
    @else
        <div></div>
    @endif

    @can('create', App\Models\Employe::class)
        <a href="{{ route('employes.create') }}"
           class="inline-flex items-center gap-2 rounded-lg bg-wine px-4 py-2 text-[12px] font-semibold text-white hover:bg-wine-dark">
            <i data-lucide="plus" class="h-4 w-4"></i> Nouvel employé
        </a>
    @endcan
</div>

@if (session('success'))
    <div class="rounded-lg bg-green-50 px-4 py-3 text-[12px] text-green-700">{{ session('success') }}</div>
@endif

<div class="overflow-x-auto rounded-xl border border-line bg-white shadow-soft">
    <table class="w-full text-left text-[12px]">
        <thead class="border-b border-line text-[10px] uppercase tracking-wider text-muted">
            <tr>
                <th class="px-4 py-3">Employé</th>
                <th class="px-4 py-3">Fonction</th>
                <th class="px-4 py-3">Téléphone</th>
                <th class="px-4 py-3">Prestations</th>
                <th class="px-4 py-3">Statut</th>
                @if ($voirPerformances)
                    <th class="px-4 py-3 text-right">Réalisées</th>
                    <th class="px-4 py-3 text-right">Montant</th>
                    <th class="px-4 py-3 text-right">Commission</th>
                @endif
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-line">
            @forelse ($employes as $e)
                @php $p = $perfs[$e->id] ?? null; @endphp
                <tr class="hover:bg-paper/60">
                    <td class="px-4 py-3 font-semibold">{{ $e->nom_complet }}</td>
                    <td class="px-4 py-3">{{ $e->fonction }}</td>
                    <td class="px-4 py-3">{{ $e->telephone ?: '—' }}</td>
                    <td class="px-4 py-3 text-muted">{{ $e->services->count() }}</td>
                    <td class="px-4 py-3">
                        <span class="rounded-full px-2 py-0.5 text-[10px] font-semibold {{ $e->is_active ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $e->is_active ? 'Actif' : 'Inactif' }}
                        </span>
                    </td>
                    @if ($voirPerformances)
                        <td class="px-4 py-3 text-right">{{ $p['prestations'] ?? 0 }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format($p['montant'] ?? 0, 0, ',', ' ') }} F</td>
                        <td class="px-4 py-3 text-right">{{ number_format($p['commission'] ?? 0, 0, ',', ' ') }} F</td>
                    @endif
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('employes.show', $e) }}" class="text-wine hover:underline">Voir</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="9" class="px-4 py-10 text-center text-muted">Aucun employé enregistré.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection