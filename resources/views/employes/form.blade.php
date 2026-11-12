@extends('layouts.app')
@section('title', $employe->exists ? 'Modifier un employé' : 'Nouvel employé')

@section('content')
@php
    $in = 'w-full rounded-lg border border-line px-3 py-2 text-[13px] outline-none focus:border-wine';
    $lb = 'mb-1 block text-[11px] font-semibold text-muted';
    $choisis = old('services', $employe->services->pluck('id')->all());
    $dispos = $employe->exists ? $employe->disponibilites->keyBy('jour_semaine') : collect();
    $voirPerf = auth()->user()->can('voirPerformances', App\Models\Employe::class);
@endphp

<form method="POST" class="mx-auto max-w-3xl space-y-5"
      action="{{ $employe->exists ? route('employes.update', $employe) : route('employes.store') }}">
    @csrf
    @if ($employe->exists) @method('PUT') @endif

    @if ($errors->any())
        <div class="rounded-lg bg-red-50 px-4 py-3 text-[12px] text-red-700">{{ $errors->first() }}</div>
    @endif

    <section class="grid gap-4 rounded-xl border border-line bg-white p-5 shadow-soft sm:grid-cols-2">
        <div><label class="{{ $lb }}">Prénom</label>
            <input name="prenom" value="{{ old('prenom', $employe->prenom) }}" class="{{ $in }}" required></div>
        <div><label class="{{ $lb }}">Nom</label>
            <input name="nom" value="{{ old('nom', $employe->nom) }}" class="{{ $in }}" required></div>
        <div><label class="{{ $lb }}">Téléphone</label>
            <input name="telephone" value="{{ old('telephone', $employe->telephone) }}" class="{{ $in }}"></div>
        <div><label class="{{ $lb }}">Fonction</label>
            <input name="fonction" value="{{ old('fonction', $employe->fonction) }}" placeholder="Esthéticienne, coiffeuse…" class="{{ $in }}" required></div>
        <div class="sm:col-span-2"><label class="{{ $lb }}">Spécialités</label>
            <textarea name="specialites" rows="2" class="{{ $in }}" placeholder="Ex : tresses, soins visage">{{ old('specialites', $employe->specialites) }}</textarea></div>
        <label class="flex items-center gap-2 text-[13px] sm:col-span-2">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $employe->is_active)) class="rounded border-line text-wine">
            Employé actif
        </label>
    </section>

    <section class="rounded-xl border border-line bg-white p-5 shadow-soft">
        <h3 class="mb-3 text-[13px] font-semibold">Horaires de travail</h3>
        <div class="space-y-2">
            @foreach (App\Models\Disponibilite::JOURS as $n => $jour)
                @php
                    $d = $dispos[$n] ?? null;
                    $actif = old("horaires.$n.actif", $d ? true : ($n <= 6 && ! $employe->exists));
                    $debut = old("horaires.$n.debut", $d ? substr($d->heure_debut, 0, 5) : '09:00');
                    $fin   = old("horaires.$n.fin", $d ? substr($d->heure_fin, 0, 5) : '18:00');
                @endphp
                <div class="flex flex-wrap items-center gap-3 text-[13px]">
                    <label class="flex w-32 items-center gap-2">
                        <input type="checkbox" name="horaires[{{ $n }}][actif]" value="1" @checked($actif) class="rounded border-line text-wine">
                        {{ $jour }}
                    </label>
                    <input type="time" name="horaires[{{ $n }}][debut]" value="{{ $debut }}" class="rounded-lg border border-line px-2 py-1.5">
                    <span class="text-muted">à</span>
                    <input type="time" name="horaires[{{ $n }}][fin]" value="{{ $fin }}" class="rounded-lg border border-line px-2 py-1.5">
                </div>
            @endforeach
        </div>
    </section>

    <section class="rounded-xl border border-line bg-white p-5 shadow-soft">
        <h3 class="mb-3 text-[13px] font-semibold">Prestations proposées</h3>
        <div class="grid gap-2 sm:grid-cols-2">
            @foreach ($services as $s)
                <label class="flex items-center gap-2 text-[13px]">
                    <input type="checkbox" name="services[]" value="{{ $s->id }}" @checked(in_array($s->id, $choisis)) class="rounded border-line text-wine">
                    {{ $s->nom }}
                </label>
            @endforeach
        </div>
    </section>

    @if ($voirPerf)
        <section class="rounded-xl border border-line bg-white p-5 shadow-soft">
            <label class="{{ $lb }}">Commission (% du montant des prestations réalisées)</label>
            <input type="number" step="0.01" min="0" max="100" name="taux_commission"
                   value="{{ old('taux_commission', $employe->taux_commission ?? 0) }}" class="{{ $in }} max-w-[160px]">
        </section>
    @endif

    <div class="flex justify-end gap-2">
        <a href="{{ route('employes.index') }}" class="rounded-lg border border-line px-4 py-2 text-[12px]">Annuler</a>
        <button class="rounded-lg bg-wine px-5 py-2 text-[12px] font-semibold text-white hover:bg-wine-dark">Enregistrer</button>
    </div>
</form>
@endsection