@php
    $ctrl = \App\Http\Controllers\ProfessionnelController::class;
    $st = $ctrl::STATUTS[$r->statut] ?? ['label' => $r->statut, 'classes' => 'bg-gray-100 text-gray-600'];
    $actions = $ctrl::TRANSITIONS[$r->statut] ?? [];
@endphp

<div class="flex flex-wrap items-center gap-4 rounded-xl border border-line bg-white p-4 shadow-soft">
    <div class="w-[88px] shrink-0 text-center">
        <div class="text-[16px] font-semibold">{{ $r->debut->format('H:i') }}</div>
        <div class="text-[12px] text-muted">{{ $r->fin->format('H:i') }}</div>
    </div>

    <div class="min-w-0 flex-1">
        <div class="font-medium">{{ $r->prenom }} {{ $r->nom }}</div>
        <div class="truncate text-[12px] text-[#716b70]">
            {{ $r->services->pluck('libelle')->implode(' · ') ?: 'Aucun service' }}
        </div>
    </div>

    <span class="rounded-full px-2.5 py-0.5 text-[11px] font-medium {{ $st['classes'] }}">{{ $st['label'] }}</span>

    <div class="flex gap-2">
        @foreach ($actions as $action)
            <form method="POST" action="{{ route('professionnel.rendez-vous.statut', $r->id) }}">
                @csrf @method('PATCH')
                <input type="hidden" name="statut" value="{{ $action }}">
                <button class="rounded-lg bg-wine px-3 py-1.5 text-[12px] font-semibold text-white hover:bg-wine-dark">
                    {{ $ctrl::ACTIONS[$action] }}
                </button>
            </form>
        @endforeach
        <a href="{{ route('professionnel.rendez-vous.show', $r->id) }}"
           class="rounded-lg border border-line px-3 py-1.5 text-[12px] text-[#716b70] hover:bg-paper">Détails</a>
    </div>
</div>
