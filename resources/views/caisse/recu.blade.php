@extends('layouts.app')
@section('title', 'Reçu ' . $numeroRecu)
@section('page-title', 'Reçu')

@push('styles')
    <style>
        .recu-carte {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 6px 24px rgba(40, 20, 25, .08);
        }

        .ticket {
            font-family: ui-monospace, 'Courier New', monospace;
            font-size: 11px;
            line-height: 1.7;
            width: 302px;
        }

        .ticket .ligne {
            display: flex;
            justify-content: space-between;
            gap: 8px;
        }

        .tirets {
            border-top: 1px dashed #c9c2c0;
            margin: 8px 0;
        }

        #recu-a4 { page: recuA4; }
        #recu-ticket { page: recuTicket; }
        @page recuA4 { size: A5; margin: 10mm; }
        @page recuTicket { size: 80mm auto; margin: 3mm; }

        @media print {
            #sidebar, #sidebar-overlay, header, .no-print { display: none !important; }
            .main-content { margin-left: 0 !important; }
            body { background: #fff !important; }
            main > div { padding: 0 !important; }
            .recu-carte { box-shadow: none !important; }
            body.imprime-a4 #recu-ticket,
            body.imprime-ticket #recu-a4 { display: none !important; }
            #recu-a4 { width: 100% !important; max-width: none !important; }
            #recu-ticket { width: 72mm !important; }
        }
    </style>
@endpush

@section('content')
    @php $f = fn($n) => number_format($n, 0, ',', ' '); @endphp

    {{-- En-tête de la page (non imprimé) --}}
    <div class="no-print flex flex-wrap items-end justify-between gap-3">
        <div>
            <div class="text-[10px] font-bold tracking-[.12em] text-muted">CAISSE &amp; PAIEMENTS</div>
            <h1 class="text-[24px] font-semibold tracking-tight">Reçu imprimable</h1>
            <p class="text-[13px] text-muted">Format A5/A4 (gauche) et ticket thermique 80 mm (droite).</p>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('caisse.index') }}"
                class="flex items-center gap-2 rounded-lg border border-line bg-white px-4 py-2 text-sm font-medium">
                <i data-lucide="arrow-left" class="h-4 w-4"></i> Caisse
            </a>

            @if ($lienWhatsapp)
                <a href="{{ $lienWhatsapp }}" target="_blank" rel="noopener"
                    class="flex items-center gap-2 rounded-lg bg-[#1f7a4d] px-4 py-2 text-sm font-medium text-white">
                    <i data-lucide="message-circle" class="h-4 w-4"></i> Envoyer par WhatsApp
                </a>
            @endif

            <button type="button" onclick="imprimer('a4')"
                class="flex items-center gap-2 rounded-lg bg-wine px-4 py-2 text-sm font-medium text-white">
                <i data-lucide="printer" class="h-4 w-4"></i> Imprimer
            </button>

            <button type="button" onclick="imprimer('ticket')"
                class="flex items-center gap-2 rounded-lg border border-line bg-white px-4 py-2 text-sm font-medium">
                Ticket 80 mm
            </button>
        </div>
    </div>

    <div class="flex flex-wrap items-start gap-6">

        {{-- ═════════ Reçu A5 / A4 ═════════ --}}
        <div id="recu-a4" class="recu-carte w-full max-w-[400px] p-7 text-[13px]">

            @if ($paiement->estAnnule())
                <div class="mb-4 rounded-lg border border-red-300 bg-red-50 p-2 text-center text-xs font-semibold text-red-700">
                    PAIEMENT ANNULÉ — {{ $paiement->motif_annulation }}
                </div>
            @endif

            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="grid h-[36px] w-[36px] place-items-center rounded-lg bg-wine text-white">
                        <span class="text-lg italic" style="font-family:'Cormorant Garamond', serif">E</span>
                    </div>
                    <div>
                        <div class="text-[19px] font-semibold leading-tight text-wine-dark"
                            style="font-family:'Cormorant Garamond', serif">{{ $nomSalon }}</div>
                        <div class="text-[11px] text-muted">Beauté &amp; bien-être</div>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-[10px] tracking-wider text-muted">REÇU</div>
                    <div class="text-[13px] font-semibold">N° {{ $numeroRecu }}</div>
                </div>
            </div>

            @if ($adresseSalon || $telephoneSalon)
                <div class="mt-3 text-[11px] text-muted">
                    {{ collect([$adresseSalon, $telephoneSalon])->filter()->join(' · ') }}
                </div>
            @endif

            <div class="tirets"></div>

            <dl class="space-y-2.5">
                <div class="flex justify-between gap-3">
                    <dt class="text-muted">Date</dt>
                    <dd>{{ $paiement->paye_le->format('d/m/Y · H:i') }}</dd>
                </div>
                <div class="flex justify-between gap-3">
                    <dt class="text-muted">Client</dt>
                    <dd class="text-right">{{ $paiement->client->prenom }} {{ $paiement->client->nom }} ({{ $codeClient }})</dd>
                </div>
                @if ($codeRdv)
                    <div class="flex justify-between gap-3">
                        <dt class="text-muted">Rendez-vous</dt>
                        <dd class="text-right">
                            {{ $codeRdv }}@if ($paiement->rendezVous?->employe) · {{ $paiement->rendezVous->employe->prenom }} {{ $paiement->rendezVous->employe->nom }}@endif
                        </dd>
                    </div>
                @endif
            </dl>

            @if ($paiement->rendezVous && $paiement->rendezVous->lignes->isNotEmpty())
                <div class="tirets"></div>
                <div class="mb-1 flex justify-between text-[11px] text-muted">
                    <span>Prestation</span><span>Montant</span>
                </div>
                <div class="space-y-1.5">
                    @foreach ($paiement->rendezVous->lignes as $ligne)
                        <div class="flex justify-between gap-3">
                            <span>{{ $ligne->libelle }}</span>
                            <span>{{ $f($ligne->prix) }}</span>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="tirets"></div>

            <div class="space-y-1.5">
                <div class="flex justify-between font-semibold">
                    <span>Total dû</span><span>{{ $f($totalDu) }} FCFA</span>
                </div>

                @foreach ($paiements as $p)
                    <div class="flex justify-between gap-3 text-[12px] text-muted">
                        <span>Payé le {{ $p->paye_le->format('d/m H:i') }} · {{ $p->mode->libelle }}@if ($p->reference) · {{ $p->reference }}@endif</span>
                        <span class="shrink-0">{{ $f($p->montant_paye) }}</span>
                    </div>
                @endforeach

                <div class="flex justify-between pt-1 font-semibold">
                    <span>Total payé</span><span>{{ $f($totalPaye) }} FCFA</span>
                </div>

                @if ($reste > 0)
                    <div class="mt-2 flex justify-between rounded-md bg-[#f7ecdb] px-3 py-2 font-semibold text-[#8a5a14]">
                        <span>Reste à payer</span><span>{{ $f($reste) }} FCFA</span>
                    </div>
                @else
                    <div class="mt-2 flex justify-between rounded-md bg-emerald-50 px-3 py-2 font-semibold text-emerald-800">
                        <span>Reste à payer</span><span>Soldé</span>
                    </div>
                @endif
            </div>

            <div class="tirets"></div>

            <dl class="space-y-2">
                @if ($pointsGagnes !== null)
                    <div class="flex justify-between gap-3">
                        <dt class="text-muted">Points fidélité gagnés</dt>
                        <dd>{{ $pointsGagnes > 0 ? '+' : '' }}{{ $pointsGagnes }} (solde {{ $f($soldePoints) }})</dd>
                    </div>
                @endif
                <div class="flex justify-between gap-3">
                    <dt class="text-muted">Encaissé par</dt>
                    <dd>{{ $paiement->encaissePar->name }}</dd>
                </div>
            </dl>

            <p class="mt-5 text-center text-[11px] text-muted">Merci de votre visite, à très bientôt ✿</p>
        </div>

        {{-- ═════════ Ticket thermique 80 mm ═════════ --}}
        <div id="recu-ticket" class="recu-carte ticket p-4">

            @if ($paiement->estAnnule())
                <div class="mb-2 border border-red-400 p-1 text-center font-bold text-red-700">** ANNULÉ **</div>
            @endif

            <div class="text-center">
                <div class="font-bold">{{ mb_strtoupper($nomSalon) }}</div>
                <div>Beauté &amp; bien-être</div>
                <div>REÇU {{ $numeroRecu }}</div>
            </div>

            <div class="tirets"></div>

            <div class="ligne"><span>{{ $paiement->paye_le->format('d/m/Y') }}</span><span>{{ $paiement->paye_le->format('H:i') }}</span></div>
            <div>Client : {{ $paiement->client->prenom }} {{ $paiement->client->nom }}</div>

            @if ($paiement->rendezVous && $paiement->rendezVous->lignes->isNotEmpty())
                <div class="tirets"></div>
                @foreach ($paiement->rendezVous->lignes as $ligne)
                    <div class="ligne"><span>{{ $ligne->libelle }}</span><span>{{ $f($ligne->prix) }}</span></div>
                @endforeach
            @endif

            <div class="tirets"></div>

            <div class="ligne font-bold"><span>TOTAL DÛ</span><span>{{ $f($totalDu) }}</span></div>
            @foreach ($paiements as $p)
                <div class="ligne">
                    <span>{{ $p->mode->libelle }} {{ $p->paye_le->format('d/m H:i') }}</span>
                    <span>{{ $f($p->montant_paye) }}</span>
                </div>
            @endforeach
            <div class="ligne font-bold">
                <span>RESTE À PAYER</span><span>{{ $reste > 0 ? $f($reste) : '0' }}</span>
            </div>

            <div class="tirets"></div>

            @if ($pointsGagnes !== null)
                <div>Fidélité : {{ $pointsGagnes > 0 ? '+' : '' }}{{ $pointsGagnes }} pts ({{ $f($soldePoints) }})</div>
            @endif
            <div>Caisse : {{ $paiement->encaissePar->name }}</div>

            <div class="mt-3 text-center">Merci et à bientôt !</div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function imprimer(format) {
            document.body.classList.add('imprime-' + format);
            window.print();
        }

        window.addEventListener('afterprint', () => {
            document.body.classList.remove('imprime-a4', 'imprime-ticket');
        });
    </script>
@endpush