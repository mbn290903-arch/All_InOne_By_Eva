@extends('layouts.app')
@section('title', 'Prestations et tarifs')
@section('page-title', 'Prestations et tarifs')

@section('content')
    @php $f = fn ($n) => number_format($n, 0, ',', ' '); @endphp

    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="text-[20px] font-semibold tracking-tight">Prestations et tarifs</h1>
            <p class="text-[12px] text-muted">Consultation seule. Seule l'administratrice peut modifier les prestations et les prix.</p>
        </div>
        <input type="search" id="recherche" placeholder="Rechercher une prestation..."
            class="w-full max-w-xs rounded-lg border border-line bg-white px-3 py-2 text-sm">
    </div>

    @forelse ($services as $categorie => $lignes)
        <section class="categorie overflow-hidden rounded-xl border border-line bg-white">
            <div class="border-b border-line bg-[#fffafb] px-4 py-3 text-[13px] font-semibold text-wine-dark">{{ $categorie }}</div>
            <table class="w-full text-left text-[13px]">
                <thead class="text-[10px] uppercase text-muted">
                    <tr>
                        <th class="px-4 py-2">Prestation</th>
                        <th class="px-4 py-2">Zone</th>
                        <th class="px-4 py-2 text-right">Durée</th>
                        <th class="px-4 py-2 text-right">Prix</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($lignes as $s)
                        @php
                            $m = (int) $s->duree_minutes;
                            $duree = $m >= 60 ? intdiv($m, 60) . ' h' . ($m % 60 ? ' ' . str_pad($m % 60, 2, '0', STR_PAD_LEFT) : '') : $m . ' min';
                        @endphp
                        <tr class="ligne border-t border-line">
                            <td class="px-4 py-2">
                                <div class="font-medium">{{ $s->nom }}</div>
                                @if ($s->description)
                                    <div class="text-[11px] text-muted">{{ $s->description }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-muted">{{ \App\Enums\ZoneSalon::tryFrom($s->zone)?->label() ?? $s->zone }}</td>
                            <td class="px-4 py-2 text-right text-muted">{{ $duree }}</td>
                            <td class="px-4 py-2 text-right font-semibold">{{ $f($s->prix) }} FCFA</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
    @empty
        <div class="rounded-xl border border-line bg-white p-8 text-center text-muted">Aucune prestation active.</div>
    @endforelse

    @if ($forfaits->isNotEmpty())
        <section class="categorie overflow-hidden rounded-xl border border-line bg-white">
            <div class="border-b border-line bg-[#fffafb] px-4 py-3 text-[13px] font-semibold text-wine-dark">Forfaits</div>
            <table class="w-full text-left text-[13px]">
                <tbody>
                    @foreach ($forfaits as $fo)
                        <tr class="ligne border-t border-line first:border-0">
                            <td class="px-4 py-2">
                                <div class="font-medium">{{ $fo->nom }}</div>
                                <div class="text-[11px] text-muted">
                                    {{ ($servicesDesForfaits[$fo->id] ?? collect())->pluck('nom')->join(' + ') }}
                                    @if ($fo->description) · {{ $fo->description }} @endif
                                </div>
                            </td>
                            <td class="px-4 py-2 text-right font-semibold">{{ $f($fo->prix) }} FCFA</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
    @endif
@endsection

@push('scripts')
    <script>
        const champ = document.getElementById('recherche');

        champ.addEventListener('input', () => {
            const mot = champ.value.trim().toLowerCase();

            document.querySelectorAll('.ligne').forEach(ligne => {
                ligne.style.display = ligne.textContent.toLowerCase().includes(mot) ? '' : 'none';
            });

            document.querySelectorAll('.categorie').forEach(bloc => {
                const visibles = [...bloc.querySelectorAll('.ligne')].some(l => l.style.display !== 'none');
                bloc.style.display = visibles ? '' : 'none';
            });
        });
    </script>
@endpush