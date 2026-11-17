@php
    $actif = 'border-wine font-semibold text-wine';
    $inactif = 'border-transparent text-muted hover:text-wine';
@endphp

<nav class="flex flex-wrap gap-1 border-b border-line">
    <a href="{{ route('caisse.index') }}"
        class="-mb-px border-b-2 px-4 py-2 text-sm {{ request()->routeIs('caisse.index') ? $actif : $inactif }}">
        Paiements
    </a>

    <a href="{{ route('caisse.impayes') }}"
        class="-mb-px flex items-center gap-2 border-b-2 px-4 py-2 text-sm {{ request()->routeIs('caisse.impayes') ? $actif : $inactif }}">
        Impayés
        @if (($nbImpayes ?? 0) > 0)
            <span class="rounded-full bg-[#fbe7e8] px-1.5 py-0.5 text-[10px] font-semibold text-[#b64e54]">{{ $nbImpayes }}</span>
        @endif
    </a>

    @if (auth()->user()->role->name === 'admin')
        <a href="{{ route('caisse.modes.index') }}"
            class="-mb-px border-b-2 px-4 py-2 text-sm {{ request()->routeIs('caisse.modes.*') ? $actif : $inactif }}">
            Modes de paiement
        </a>
    @endif
</nav>