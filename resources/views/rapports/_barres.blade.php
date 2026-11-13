@php
    $max = max(1, collect($points)->max('valeur'));
    $n = count($points);
    $pas = max(1, (int) ceil($n / 10));
@endphp

@if ($n === 0)
    <div class="py-8 text-center text-[12px] text-muted">Aucune donnée sur cette période.</div>
@else
    <div class="mb-1 text-[10px] text-muted">Maximum : {{ number_format($max, 0, ',', ' ') }}</div>
    <div class="overflow-x-auto">
        <div style="min-width: {{ max(260, $n * 20) }}px">
            <div class="flex h-[150px] items-end gap-[3px] border-b border-line">
                @foreach ($points as $p)
                    <div class="flex h-full flex-1 items-end"
                        title="{{ $p['libelle'] }} : {{ number_format($p['valeur'], 0, ',', ' ') }}">
                        <div class="w-full rounded-t"
                            style="height: {{ round($p['valeur'] / $max * 100) }}%; background: {{ $couleur }}; min-height: {{ $p['valeur'] > 0 ? '2px' : '0' }}">
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-1 flex gap-[3px]">
                @foreach ($points as $i => $p)
                    <div class="flex-1 whitespace-nowrap text-center text-[9px] text-muted">
                        {{ $i % $pas === 0 ? $p['court'] : '' }}
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif