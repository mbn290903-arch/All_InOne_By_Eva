@extends('layouts.app')
@section('title', $client->prenom . ' ' . $client->nom . ' — All in One by Eva')
@section('breadcrumb')
    <a href="{{ route('clients.index') }}" class="hover:underline">Clients</a> / <span class="text-neutral-900">{{ $client->prenom }} {{ $client->nom }}</span>
@endsection

@php
    $val = fn ($v) => $v instanceof \BackedEnum
        ? $v->value
        : (string) ($v ?? '');

    $statut = $val($client->statut);
    $public = $val($client->public);
    $canal = $val($client->canal_prefere);

    $code = $client->code
        ?? 'CL-' . str_pad($client->id, 6, '0', STR_PAD_LEFT);

    $ini = mb_strtoupper(
        mb_substr($client->prenom, 0, 1)
        . mb_substr($client->nom, 0, 1)
    );

    $fmt = fn ($n) => preg_replace(
        '/^(\d{2})(\d{3})(\d{2})(\d{2})$/',
        '$1 $2 $3 $4',
        (string) $n
    );

    $wa = $client->whatsapp ?: $client->telephone;
    $archive = $client->archived_at;

    $naiss = $client->date_naissance
        ? \Illuminate\Support\Carbon::parse($client->date_naissance)
        : null;

    $inscr = \Illuminate\Support\Carbon::parse(
        $client->date_inscription ?? $client->created_at
    );

    $jours = null;

    if ($naiss) {
        $prochain = $naiss->copy()->year(now()->year);

        if ($prochain->lt(now()->startOfDay())) {
            $prochain->addYear();
        }

        $jours = (int) now()->startOfDay()->diffInDays($prochain);
    }

    $statutStyle = [
        'actif' => 'bg-emerald-100 text-emerald-800',
        'inactif' => 'bg-amber-100 text-amber-800',
    ];
@endphp
@section('content')
<div class="mx-auto max-w-6xl space-y-6">

    {{-- ===== Bandeau d'archivage ===== --}}
    @if ($archive)
        <div role="status" class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-neutral-300 bg-neutral-100 px-5 py-4 text-sm">
            <p><strong class="font-semibold">Fiche archivée</strong> depuis le {{ $archive->format('d/m/Y') }}. L'historique est conservé ; la fiche n'apparaît plus dans les listes courantes.</p>
            @can('archive', $client)
                <form method="POST" action="{{ route('clients.restore', $client) }}">
                    @csrf
                    <button type="submit" class="rounded-lg border border-neutral-300 bg-white px-4 py-2 text-sm font-medium hover:bg-neutral-50">Restaurer la fiche</button>
                </form>
            @endcan
        </div>
    @endif

    {{-- ===== En-tête ===== --}}
    <section class="flex flex-wrap items-center justify-between gap-5 rounded-xl border border-neutral-200 bg-white p-6">
        <div class="flex items-center gap-5">
            <span class="flex h-16 w-16 shrink-0 items-center justify-center rounded-full bg-brand-100 text-xl font-semibold text-brand-700">{{ $ini }}</span>
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <h1 class="text-2xl font-semibold tracking-tight">{{ $client->prenom }} {{ $client->nom }}</h1>
                    <span class="rounded-full px-2.5 py-0.5 text-xs font-medium {{ $statutStyle[$statut] ?? 'bg-neutral-100 text-neutral-600' }}">{{ ucfirst($statut) }}</span>
                    @if ($archive)
                        <span class="rounded-full bg-neutral-200 px-2.5 py-0.5 text-xs font-medium text-neutral-700">Archivé</span>
                    @endif
                    @if ($public === 'femme')
                        <span class="rounded-full bg-brand-50 px-2.5 py-0.5 text-xs font-medium text-brand-700">Accès espace femmes</span>
                    @endif
                </div>
                <p class="mt-1.5 text-sm text-neutral-500">{{ $code }} · Cliente depuis {{ $inscr->translatedFormat('F Y') }}</p>
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            <button type="button" id="open-wa" @disabled(! $wa)
                    class="rounded-lg bg-emerald-700 px-4 py-2.5 text-sm font-medium text-white hover:bg-emerald-800 disabled:opacity-50">WhatsApp</button>
            <a href="{{ route('rendez-vous.create') }}"
                    class="flex items-center gap-2 rounded-lg bg-wine px-3 py-2 text-[12px] font-medium text-white"><i
                        data-lucide="calendar-plus" class="h-3.5 w-3.5"></i> Nouveau RDV</a>
            <button type="button" disabled title="Bientôt disponible" class="rounded-lg border border-neutral-300 px-4 py-2.5 text-sm font-medium text-neutral-400">Encaisser</button>
            @unless ($archive)
                <a href="{{ route('clients.edit', $client) }}" class="rounded-lg border border-neutral-300 px-4 py-2.5 text-sm font-medium hover:bg-neutral-50">Modifier</a>
            @endunless
        </div>
    </section>

    <div class="grid gap-6 lg:grid-cols-[22rem_1fr]">

        {{-- ===== Colonne gauche ===== --}}
        <div class="space-y-6">

            <section class="rounded-xl border border-neutral-200 bg-white">
                <h2 class="border-b border-neutral-200 px-6 py-4 font-semibold">Informations</h2>
                <dl class="space-y-3 px-6 py-5 text-sm">
                    @foreach ([
                        'Téléphone' => '+221 ' . $fmt($client->telephone),
                        'WhatsApp'  => $client->whatsapp ? '+221 ' . $fmt($client->whatsapp) : 'Non renseigné',
                        'E-mail'    => $client->email ?: 'Non renseigné',
                    ] as $k => $v)
                        <div class="flex justify-between gap-4"><dt class="text-neutral-500">{{ $k }}</dt><dd class="text-right">{{ $v }}</dd></div>
                    @endforeach
                    <div class="flex justify-between gap-4">
                        <dt class="text-neutral-500">Naissance</dt>
                        <dd class="text-right">
                            @if ($naiss)
                                {{ $naiss->translatedFormat('j F Y') }}
                                @if ($jours !== null && $jours <= 30)<span class="text-brand-600">· anniv. dans {{ $jours }} j</span>@endif
                            @else Non renseignée @endif
                        </dd>
                    </div>
                    <div class="flex justify-between gap-4"><dt class="text-neutral-500">Inscription</dt><dd>{{ $inscr->translatedFormat('j F Y') }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-neutral-500">Public</dt><dd>{{ ucfirst($public) }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-neutral-500">Canal préféré</dt><dd>{{ ['whatsapp' => 'WhatsApp', 'appel' => 'Appel', 'sms' => 'SMS'][$canal] ?? '—' }}</dd></div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-neutral-500">Marketing</dt>
                        <dd class="text-right">
                            @if ($client->consent_promotions)
                                <span class="text-emerald-700">✓ Consentement{{ $client->consent_at ? ' (' . \Illuminate\Support\Carbon::parse($client->consent_at)->format('d/m/Y') . ')' : '' }}</span>
                            @else
                                <span class="text-neutral-500">Pas de consentement</span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </section>

            <section class="rounded-xl border border-neutral-200 bg-white">
                <h2 class="border-b border-neutral-200 px-6 py-4 font-semibold">Notes</h2>
                <div class="px-6 py-5 text-sm">
                    @if ($client->notes)
                        <p class="leading-relaxed">{!! nl2br(e($client->notes)) !!}</p>
                    @else
                        <p class="text-neutral-500">Aucune note pour cette cliente.</p>
                    @endif
                </div>
            </section>

            <section class="rounded-xl border border-neutral-200 bg-white">
                <h2 class="border-b border-neutral-200 px-6 py-4 font-semibold">Fidélité</h2>
                <div class="px-6 py-5">
                    <p class="text-3xl font-semibold text-brand-700">0 <span class="text-sm font-normal text-neutral-500">points</span></p>
                    <p class="mt-1 text-sm text-neutral-500">Les points s'ajouteront après les premières prestations encaissées.</p>
                </div>
            </section>
        </div>

        {{-- ===== Colonne droite ===== --}}
        <div class="min-w-0 space-y-6">

            <div class="grid grid-cols-2 gap-4 xl:grid-cols-4">
                @foreach ([['Visites', '0', ''], ['Total dépensé', '0', 'FCFA'], ['Panier moyen', '—', 'FCFA'], ['Reste à payer', '0', 'FCFA']] as [$t, $n, $u])
                    <div class="rounded-xl border border-neutral-200 bg-white p-5">
                        <p class="text-sm text-neutral-500">{{ $t }}</p>
                        <p class="mt-2 text-2xl font-semibold">{{ $n }}</p>
                        <p class="mt-1 text-xs text-neutral-500">{{ $u }}</p>
                    </div>
                @endforeach
            </div>

            <section class="rounded-xl border border-neutral-200 bg-white">
                <h2 class="border-b border-neutral-200 px-6 py-4 font-semibold">Visites &amp; prestations</h2>
                <div class="px-6 py-12 text-center">
                    <p class="font-medium">Aucune visite enregistrée</p>
                    <p class="mt-1 text-sm text-neutral-500">Les rendez-vous et prestations de cette cliente apparaîtront ici.</p>
                </div>
            </section>

            <section class="rounded-xl border border-neutral-200 bg-white">
                <div class="flex flex-wrap items-baseline justify-between gap-2 border-b border-neutral-200 px-6 py-4">
                    <h2 class="font-semibold">Historique des paiements</h2>
                    <p class="text-xs text-neutral-500">Les corrections sont tracées et ne suppriment jamais l'opération d'origine.</p>
                </div>
                <div class="px-6 py-12 text-center">
                    <p class="font-medium">Aucun paiement</p>
                    <p class="mt-1 text-sm text-neutral-500">Les encaissements s'afficheront ici avec leur reçu.</p>
                </div>
            </section>
        </div>
    </div>
</div>

{{-- ===== Fenêtre WhatsApp ===== --}}
<dialog id="wa-modal" class="w-full max-w-lg rounded-xl p-0 shadow-xl backdrop:bg-black/40">
    <div class="flex items-start justify-between border-b border-neutral-200 px-6 py-4">
        <div>
            <h2 class="text-lg font-semibold">Message WhatsApp</h2>
            <p class="text-xs text-neutral-500">À {{ $client->prenom }} {{ $client->nom }} · +221 {{ $fmt($wa) }}</p>
        </div>
        <button type="button" id="wa-close" aria-label="Fermer" class="rounded p-1 text-neutral-500 hover:bg-neutral-100">✕</button>
    </div>
    <div class="space-y-4 px-6 py-5">
        <div>
            <label for="wa-model" class="mb-1.5 block text-[13px] font-medium">Modèle</label>
            <select id="wa-model" class="w-full rounded-lg border border-neutral-200 px-3 py-2.5 text-sm">
                <option value="rappel">Rappel de rendez-vous</option>
                <option value="merci">Remerciement après visite</option>
                <option value="anniv">Anniversaire</option>
                <option value="libre">Message libre</option>
            </select>
        </div>
        <div>
            <label for="wa-text" class="mb-1.5 block text-[13px] font-medium">Message <span class="font-normal text-neutral-400">— modifiable avant ouverture</span></label>
            <textarea id="wa-text" rows="5" class="w-full rounded-lg border border-neutral-200 px-3 py-2.5 text-sm focus:border-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-600/20"></textarea>
            <p class="mt-1.5 text-xs text-neutral-500">Variable remplacée : {prenom}</p>
        </div>
        <p class="rounded-lg bg-neutral-50 px-3 py-2 text-xs text-neutral-500">L'application prépare un lien WhatsApp prérempli. L'envoi se fait par vous dans WhatsApp.</p>
    </div>
    <div class="flex justify-end gap-2 border-t border-neutral-200 px-6 py-4">
        <button type="button" id="wa-cancel" class="rounded-lg border border-neutral-300 px-4 py-2.5 text-sm font-medium hover:bg-neutral-50">Annuler</button>
        <button type="button" id="wa-send" class="rounded-lg bg-emerald-700 px-4 py-2.5 text-sm font-medium text-white hover:bg-emerald-800">Ouvrir WhatsApp</button>
    </div>
</dialog>

{{-- Message de confirmation (après modification ou restauration) --}}
@if (session('status'))
    <div role="status" class="fixed bottom-6 right-6 z-[9999] w-[calc(100%-3rem)] max-w-sm rounded-xl bg-[#76243D] px-5 py-4 text-sm text-white shadow-lg">
        <strong class="block font-semibold">{{ session('status')[0] }}</strong>
        <span class="text-neutral-300">{{ session('status')[1] }}</span>
    </div>
@endif

<script>
    const dlg = document.getElementById('wa-modal');
    const sel = document.getElementById('wa-model');
    const txt = document.getElementById('wa-text');
    const prenom = @json($client->prenom);
    const numero = '221' + @json(preg_replace('/\D/', '', (string) $wa));

    const modeles = {
        rappel: "Bonjour {prenom}, petit rappel : nous vous attendons bientôt chez All in One by Eva. Répondez OUI pour confirmer votre rendez-vous. À bientôt !",
        merci:  "Bonjour {prenom}, merci pour votre visite chez All in One by Eva. Nous espérons que tout vous a plu. À très bientôt !",
        anniv:  "Bonjour {prenom}, toute l'équipe de All in One by Eva vous souhaite un très joyeux anniversaire ! Passez nous voir pour une surprise.",
        libre:  "Bonjour {prenom}, ",
    };
    const remplir = () => { txt.value = modeles[sel.value].replaceAll('{prenom}', prenom); };

    document.getElementById('open-wa').addEventListener('click', () => { remplir(); dlg.showModal(); });
    sel.addEventListener('change', remplir);
    ['wa-close', 'wa-cancel'].forEach(id => document.getElementById(id).addEventListener('click', () => dlg.close()));
    document.getElementById('wa-send').addEventListener('click', () => {
        window.open('https://wa.me/' + numero + '?text=' + encodeURIComponent(txt.value), '_blank', 'noopener');
        dlg.close();
    });
</script>
@endsection
