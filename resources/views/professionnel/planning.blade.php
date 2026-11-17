@extends('layouts.app')
@section('title', 'Mon planning')
@section('page-title', 'Mon planning')

@section('content')
@if (session('success'))
    <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-[13px] text-emerald-700">{{ session('success') }}</div>
@endif
@if ($errors->any())
    <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-2.5 text-[13px] text-red-700">{{ $errors->first() }}</div>
@endif

@if (! $employeLie)
    <div class="rounded-xl border border-[#f1d9ae] bg-[#fff8ec] p-5 text-[13px] text-[#8a5a1a]">
        Votre compte n’est pas encore relié à une fiche employé. Demandez à l’administrateur de faire le lien.
    </div>
@else
    <div class="flex flex-wrap items-center justify-between gap-2">
        <div class="text-[15px] font-medium">
            Semaine du {{ $debut->translatedFormat('d F') }} au {{ $fin->translatedFormat('d F Y') }}
        </div>
        <div class="flex gap-2 text-[13px]">
            <a href="{{ route('professionnel.planning', ['semaine' => $debut->copy()->subWeek()->toDateString()]) }}" class="rounded-lg border border-line bg-white px-3 py-1.5 hover:bg-paper">← Précédente</a>
            <a href="{{ route('professionnel.planning') }}" class="rounded-lg border border-line bg-white px-3 py-1.5 hover:bg-paper">Cette semaine</a>
            <a href="{{ route('professionnel.planning', ['semaine' => $debut->copy()->addWeek()->toDateString()]) }}" class="rounded-lg border border-line bg-white px-3 py-1.5 hover:bg-paper">Suivante →</a>
        </div>
    </div>

    @for ($i = 0; $i < 7; $i++)
        @php
            $jour = $debut->copy()->addDays($i);
            $rdvs = $parJour->get($jour->toDateString(), collect());
        @endphp
        <section>
            <h2 class="mb-2 flex items-center gap-2 text-[13px] font-semibold {{ $jour->isToday() ? 'text-wine' : 'text-[#716b70]' }}">
                {{ ucfirst($jour->translatedFormat('l d F')) }}
                @if ($jour->isToday())<span class="rounded-full bg-[#f6e9ed] px-2 py-0.5 text-[10px]">Aujourd’hui</span>@endif
                <span class="font-normal text-muted">· {{ $rdvs->count() }} rendez-vous</span>
            </h2>
            <div class="space-y-2">
                @foreach ($rdvs as $r)
                    @include('professionnel._carte-rdv', ['r' => $r])
                @endforeach
            </div>
        </section>
    @endfor
@endif
@endsection
