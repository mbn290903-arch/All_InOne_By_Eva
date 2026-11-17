@extends('layouts.app')

@section('title', 'Tableau de bord — Réception')
@section('page-title', 'Tableau de bord')

@push('styles')
    <style>
        .panel {
            background: #fff;
            border: 1px solid #eae5e2;
            border-radius: 11px;
            box-shadow: 0 1px 3px rgba(40, 20, 25, .035);
        }

        .icon-tile {
            width: 29px;
            height: 29px;
            display: grid;
            place-items: center;
            border-radius: 8px;
            background: #f7ecef;
            color: #76243d;
            flex-shrink: 0;
        }

        .icon-tile svg {
            width: 15px;
            height: 15px;
        }

        .section-label {
            color: #827a7f;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .12em;
        }
    </style>
@endpush

@section('content')
    @php
        $f = fn($n) => number_format($n, 0, ',', ' ');
        $minutes = fn($rdv) => (int) $rdv->debut->diffInMinutes($rdv->fin);
    @endphp

    @include('partials.alertes')

    {{-- Titre et actions --}}
    <section class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-[19px] font-semibold tracking-tight">
            Bonjour {{ auth()->user()->name }}
            <span class="font-normal text-[#8b8588]">— votre journée à la réception</span>
        </h1>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('clients.create') }}"
                class="flex items-center gap-2 rounded-lg border border-[#ded8d6] bg-white px-3 py-2 text-[12px] font-medium">
                <i data-lucide="user-round-plus" class="h-3.5 w-3.5"></i> Nouveau client
            </a>
            <a href="{{ route('rendez-vous.create') }}"
                class="flex items-center gap-2 rounded-lg bg-wine px-3 py-2 text-[12px] font-medium text-white">
                <i data-lucide="calendar-plus" class="h-3.5 w-3.5"></i> Nouveau rendez-vous
            </a>
            @if ($peutEncaisser)
                <a href="{{ route('caisse.create') }}"
                    class="flex items-center gap-2 rounded-lg border border-wine px-3 py-2 text-[12px] font-medium text-wine">
                    <i data-lucide="wallet" class="h-3.5 w-3.5"></i> Encaisser
                </a>
            @endif
        </div>
    </section>

    {{-- Indicateurs du jour --}}
    <section>
        <div class="mb-3 flex items-center gap-3"><span class="section-label">AUJOURD'HUI</span>
            <div class="h-px flex-1 bg-line"></div>
        </div>
        <div class="grid grid-cols-2 gap-3 xl:grid-cols-5">
            <div class="panel p-4">
                <div class="mb-3 flex items-start justify-between">
                    <div class="text-[12px] font-medium text-[#817a7e]">Rendez-vous du jour</div>
                    <div class="icon-tile"><i data-lucide="calendar-days"></i></div>
                </div>
                <div class="text-[22px] font-bold">{{ $totalJour }}</div>
                <div class="mt-1 text-[12px] text-muted">{{ $termines }} terminés</div>
            </div>
            <div class="panel p-4">
                <div class="mb-3 flex items-start justify-between">
                    <div class="text-[12px] font-medium text-[#817a7e]">Encore à venir</div>
                    <div class="icon-tile"><i data-lucide="calendar-clock"></i></div>
                </div>
                <div class="text-[22px] font-bold">{{ $restants }}</div>
                <div class="mt-1 text-[12px] text-muted">d'ici la fin de la journée</div>
            </div>
            <div class="panel p-4">
                <div class="mb-3 flex items-start justify-between">
                    <div class="text-[12px] font-medium text-[#817a7e]">En attente de confirmation</div>
                    <div class="icon-tile bg-[#fff2df] text-[#bf812f]"><i data-lucide="hourglass"></i></div>
                </div>
                <div class="text-[22px] font-bold">{{ $enAttente }}</div>
                <div class="mt-1 text-[12px] text-muted">rendez-vous du jour</div>
            </div>
            <div class="panel p-4">
                <div class="mb-3 flex items-start justify-between">
                    <div class="text-[12px] font-medium text-[#817a7e]">Clients enregistrés</div>
                    <div class="icon-tile"><i data-lucide="user-plus"></i></div>
                </div>
                <div class="text-[22px] font-bold">{{ $clientsDuJour }}</div>
                <div class="mt-1 text-[12px] text-muted">aujourd'hui</div>
            </div>
            @if ($peutEncaisser)
                <div class="panel p-4">
                    <div class="mb-3 flex items-start justify-between">
                        <div class="text-[12px] font-medium text-[#817a7e]">Mes encaissements</div>
                        <div class="icon-tile"><i data-lucide="banknote"></i></div>
                    </div>
                    <div class="text-[22px] font-bold">{{ $f($mesEncaissements) }} <span
                            class="text-[10px] font-medium text-muted">FCFA</span></div>
                    <div class="mt-1 text-[12px] text-muted">{{ $mesPaiements }} paiement(s)</div>
                </div>
            @endif
        </div>
    </section>

    {{-- Planning du jour et à encaisser --}}
    <section class="grid grid-cols-1 gap-3 xl:grid-cols-[1.4fr_1fr]">

        <div class="panel overflow-hidden">
            <div class="flex items-center justify-between border-b border-line px-4 py-3">
                <h2 class="text-[13px] font-semibold">Planning du jour</h2>
                <a href="{{ route('rendez-vous.index', ['vue' => 'jour']) }}"
                    class="text-[12px] font-medium text-wine">Ouvrir le planning →</a>
            </div>
            <div class="divide-y divide-line">
                @forelse ($rdvDuJour as $rdv)
                    @php
                        $m = $minutes($rdv);
                        $duree =
                            $m >= 60
                                ? intdiv($m, 60) . ' h' . ($m % 60 ? ' ' . str_pad($m % 60, 2, '0', STR_PAD_LEFT) : '')
                                : $m . ' min';
                    @endphp
                    <div class="flex gap-3 px-4 py-3">
                        <div class="w-[48px] shrink-0">
                            <div class="text-[13px] font-semibold">{{ $rdv->debut->format('H:i') }}</div>
                            <div class="text-[12px] text-muted">{{ $duree }}</div>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="truncate text-[13px] font-semibold">{{ $rdv->client->prenom }}
                                {{ $rdv->client->nom }}</div>
                            <div class="truncate text-[11px] text-muted">
                                {{ $rdv->libellePrestations() ?: 'Prestation à préciser' }} ·
                                {{ $rdv->employe ? $rdv->employe->prenom . ' ' . $rdv->employe->nom : 'Non attribué' }}
                            </div>
                        </div>
                        <span class="h-fit shrink-0 rounded-full px-2 py-1 text-[11px] {{ $rdv->statut->badge() }}">●
                            {{ $rdv->statut->label() }}</span>
                    </div>
                @empty
                    <div class="px-4 py-8 text-center text-[12px] text-muted">Aucun rendez-vous aujourd'hui.</div>
                @endforelse
            </div>
        </div>

        @if ($peutEncaisser)
            <div class="panel overflow-hidden">
                <div class="flex items-center justify-between border-b border-line px-4 py-3">
                    <h2 class="text-[13px] font-semibold">À encaisser aujourd'hui</h2>
                    <a href="{{ route('caisse.impayes') }}" class="text-[12px] font-medium text-wine">Tous les impayés
                        →</a>
                </div>
                <div class="divide-y divide-line">
                    @forelse ($aEncaisser as $rdv)
                        <div class="flex items-center gap-3 px-4 py-3">
                            <div class="min-w-0 flex-1">
                                <div class="truncate text-[13px] font-semibold">{{ $rdv->client->prenom }}
                                    {{ $rdv->client->nom }}</div>
                                <div class="truncate text-[11px] text-muted">
                                    {{ $rdv->debut->format('H:i') }} · reste
                                    {{ $f($rdv->prix_prevu - (int) $rdv->paye) }} FCFA
                                </div>
                            </div>
                            <a href="{{ route('caisse.create', ['rendez_vous_id' => $rdv->id]) }}"
                                class="rounded-lg bg-wine px-3 py-1.5 text-[11px] text-white">Encaisser</a>
                        </div>
                    @empty
                        <div class="px-4 py-8 text-center text-[12px] text-muted">Rien à encaisser pour aujourd'hui.</div>
                    @endforelse
                </div>
            </div>
        @endif
    </section>

    {{-- WhatsApp manuel --}}
    <section class="grid grid-cols-1 gap-3 lg:grid-cols-2">

        <div class="panel overflow-hidden">
            <div class="flex items-center justify-between border-b border-line px-4 py-3">
                <h2 class="text-[13px] font-semibold">Rappels WhatsApp · rendez-vous de demain</h2>
                <span class="text-[11px] text-muted">{{ $rappelsDemain->count() }}</span>
            </div>
            <div class="divide-y divide-line">
                @forelse ($rappelsDemain as $item)
                    @php $rdv = $item['rdv']; @endphp
                    <div class="flex items-center gap-3 px-4 py-3">
                        <div class="w-[44px] shrink-0 text-[13px] font-semibold">{{ $rdv->debut->format('H:i') }}</div>
                        <div class="min-w-0 flex-1">
                            <div class="truncate text-[13px] font-semibold">{{ $rdv->client->prenom }}
                                {{ $rdv->client->nom }}</div>
                            <div class="truncate text-[11px] text-muted">
                                {{ $rdv->libellePrestations() ?: 'Prestation à préciser' }} · {{ $rdv->statut->label() }}
                            </div>
                        </div>
                        @if ($item['lien'])
                            <a href="{{ $item['lien'] }}" target="_blank" rel="noopener"
                                class="flex items-center gap-1.5 rounded-lg bg-[#1f7a4d] px-3 py-1.5 text-[11px] text-white">
                                <i data-lucide="message-circle" class="h-3.5 w-3.5"></i> Envoyer
                            </a>
                        @else
                            <span class="text-[11px] text-muted">Pas de numéro</span>
                        @endif
                    </div>
                @empty
                    <div class="px-4 py-8 text-center text-[12px] text-muted">Aucun rendez-vous demain.</div>
                @endforelse
            </div>
        </div>

        <div class="panel overflow-hidden">
            <div class="flex items-center justify-between border-b border-line px-4 py-3">
                <h2 class="text-[13px] font-semibold">Anniversaires du jour</h2>
                <span class="text-[11px] text-muted">{{ $anniversaires->count() }}</span>
            </div>
            <div class="divide-y divide-line">
                @forelse ($anniversaires as $item)
                    <div class="flex items-center gap-3 px-4 py-3">
                        <div class="icon-tile bg-[#fff1df] text-[#bd812f]"><i data-lucide="cake"></i></div>
                        <div class="min-w-0 flex-1">
                            <div class="truncate text-[13px] font-semibold">{{ $item['client']->prenom }}
                                {{ $item['client']->nom }}</div>
                            <div class="text-[11px] text-muted">{{ $item['age'] }} ans aujourd'hui</div>
                        </div>
                        @if (auth()->user()->isAdmin() || auth()->user()->hasPermission('whatsapp.envoyer'))
                            @if ($item['lien'])
                                <a href="{{ $item['lien'] }}" target="_blank" rel="noopener"
                                    class="flex items-center gap-1.5 rounded-lg bg-[#1f7a4d] px-3 py-1.5 text-[11px] text-white">
                                    <i data-lucide="message-circle" class="h-3.5 w-3.5"></i> Envoyer
                                </a>
                            @else
                                <span class="text-[11px] text-muted">Pas de numéro</span>
                            @endif
                        @endif
                    </div>
                @empty
                    <div class="px-4 py-8 text-center text-[12px] text-muted">Aucun anniversaire aujourd'hui.</div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- Raccourcis --}}
    <section>
        <div class="mb-3 flex items-center gap-3"><span class="section-label">RACCOURCIS</span>
            <div class="h-px flex-1 bg-line"></div>
        </div>
        <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
            @foreach ([['Clients', 'users', route('clients.index')], ['Planning des rendez-vous', 'calendar', route('rendez-vous.index')], ['Prestations et tarifs', 'sparkles', route('catalogue.index')], ['Caisse', 'wallet', route('caisse.index')]] as [$titre, $icone, $lien])
                <a href="{{ $lien }}" class="panel flex items-center gap-3 p-4 hover:bg-[#fffafb]">
                    <div class="icon-tile"><i data-lucide="{{ $icone }}"></i></div>
                    <span class="text-[13px] font-medium">{{ $titre }}</span>
                </a>
            @endforeach
        </div>
    </section>

@endsection
