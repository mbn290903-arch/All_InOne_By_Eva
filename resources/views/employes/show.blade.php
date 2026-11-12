@extends('layouts.app')
@section('title', $employe->nom_complet)

@section('content')
@php $dispos = $employe->disponibilites->keyBy('jour_semaine'); @endphp

@if (session('success'))
    <div class="rounded-lg bg-green-50 px-4 py-3 text-[12px] text-green-700">{{ session('success') }}</div>
@endif

<div class="grid gap-5 lg:grid-cols-3">
    <section class="space-y-3 rounded-xl border border-line bg-white p-5 shadow-soft">
        <div class="flex items-center gap-3">
            <div class="grid h-12 w-12 place-items-center rounded-full bg-[#f0dfe4] font-semibold text-wine">
                {{ strtoupper(mb_substr($employe->prenom, 0, 1) . mb_substr($employe->nom, 0, 1)) }}
            </div>
            <div>
                <div class="font-semibold">{{ $employe->nom_complet }}</div>
                <div class="text-[12px] text-muted">{{ $employe->fonction }}</div>
            </div>
        </div>

        <div class="text-[13px]"><span class="text-muted">Téléphone :</span> {{ $employe->telephone ?: '—' }}</div>
        <div class="text-[13px]"><span class="text-muted">Spécialités :</span> {{ $employe->specialites ?: '—' }}</div>
        <div class="text-[13px]"><span class="text-muted">Statut :</span> {{ $employe->is_active ? 'Actif' : 'Inactif' }}</div>

        <div>
            <div class="mb-1 text-[11px] font-semibold text-muted">Horaires</div>
            @foreach (App\Models\Disponibilite::JOURS as $n => $jour)
                @php $d = $dispos[$n] ?? null; @endphp
                <div class="flex justify-between text-[12px]">
                    <span>{{ $jour }}</span>
                    <span class="text-muted">{{ $d ? substr($d->heure_debut, 0, 5).' – '.substr($d->heure_fin, 0, 5) : 'Repos' }}</span>
                </div>
            @endforeach
        </div>

        @can('update', App\Models\Employe::class)
            <a href="{{ route('employes.edit', $employe) }}"
               class="block rounded-lg border border-line py-2 text-center text-[12px] hover:bg-paper">Modifier</a>
        @endcan
    </section>

    <div class="space-y-5 lg:col-span-2">
        <section class="rounded-xl border border-line bg-white p-5 shadow-soft">
            <h3 class="mb-3 text-[13px] font-semibold">Prestations proposées</h3>
            <div class="flex flex-wrap gap-2">
                @forelse ($employe->services as $s)
                    <span class="rounded-full bg-[#f6e9ed] px-3 py-1 text-[11px] text-wine">{{ $s->nom }}</span>
                @empty
                    <span class="text-[12px] text-muted">Aucune prestation affectée.</span>
                @endforelse
            </div>
        </section>

        @if ($perf)
            <section class="rounded-xl border border-line bg-white p-5 shadow-soft">
                <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                    <h3 class="text-[13px] font-semibold">Performance &amp; rémunération</h3>
                    <form method="GET" class="flex items-center gap-2 text-[12px]">
                        <input type="date" name="du" value="{{ $du->toDateString() }}" class="rounded-lg border border-line px-2 py-1">
                        <input type="date" name="au" value="{{ $au->toDateString() }}" class="rounded-lg border border-line px-2 py-1">
                        <button class="rounded-lg border border-line px-3 py-1 hover:bg-paper">OK</button>
                    </form>
                </div>

                <div class="grid grid-cols-2 gap-3 text-center sm:grid-cols-4">
                    <div class="rounded-lg bg-paper p-3">
                        <div class="text-[10px] text-muted">Rendez-vous</div>
                        <div class="text-lg font-semibold">{{ $perf['rendez_vous'] }}</div>
                    </div>
                    <div class="rounded-lg bg-paper p-3">
                        <div class="text-[10px] text-muted">Prestations</div>
                        <div class="text-lg font-semibold">{{ $perf['prestations'] }}</div>
                    </div>
                    <div class="rounded-lg bg-paper p-3">
                        <div class="text-[10px] text-muted">Montant généré</div>
                        <div class="text-lg font-semibold">{{ number_format($perf['montant'], 0, ',', ' ') }} F</div>
                    </div>
                    <div class="rounded-lg bg-paper p-3">
                        <div class="text-[10px] text-muted">Commission ({{ rtrim(rtrim(number_format($employe->taux_commission, 2, '.', ''), '0'), '.') }} %)</div>
                        <div class="text-lg font-semibold text-wine">{{ number_format($perf['commission'], 0, ',', ' ') }} F</div>
                    </div>
                </div>
            </section>
        @endif
    </div>
</div>
@endsection