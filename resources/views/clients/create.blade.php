@extends('layouts.app')
@section('title', 'Ajouter un client — All in One by Eva')
@section('breadcrumb', 'Clients / Ajouter un client')

@php
    $inp = 'w-full rounded-lg border bg-white px-3 py-2.5 text-sm placeholder:text-neutral-400 focus:outline-none focus:ring-2 ';
    $ok  = 'border-neutral-200 focus:border-brand-600 focus:ring-brand-600/20';
    $bad = 'border-red-300 bg-red-50/40 focus:ring-red-200';
    $cls = fn($f) => $inp . ($errors->has($f) ? $bad : $ok);
    $labels = ['prenom' => 'Prénom', 'nom' => 'Nom', 'telephone' => 'Téléphone', 'whatsapp' => 'WhatsApp', 'email' => 'E-mail', 'date_naissance' => 'Date de naissance'];
    $doublon = session('doublon');
@endphp

@section('content')
<form method="POST" action="{{ route('clients.store') }}" id="client-form" class="mx-auto max-w-5xl" novalidate>
    @csrf

    {{-- En-tête --}}
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight">Ajouter un client</h1>
            <p class="mt-1 text-sm text-neutral-500">Les champs marqués <span class="text-brand-600">*</span> sont obligatoires.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('clients.index') }}" class="rounded-lg border border-neutral-300 bg-white px-4 py-2.5 text-sm font-medium hover:bg-neutral-50">Annuler</a>
            <button type="submit" class="rounded-lg bg-brand-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-700">Enregistrer le client</button>
        </div>
    </div>

    {{-- Résumé des erreurs --}}
    @if ($errors->any())
        <div role="alert" class="mt-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <strong class="font-semibold">{{ $errors->count() }} champ{{ $errors->count() > 1 ? 's' : '' }} à corriger</strong> avant l'enregistrement :
            @foreach (array_keys($errors->messages()) as $f)
                <a href="#{{ $f }}" class="underline">{{ $labels[$f] ?? $f }}</a>{{ $loop->last ? '.' : ',' }}
            @endforeach
        </div>
    @endif

    <div class="mt-6 divide-y divide-neutral-200 rounded-xl border border-neutral-200 bg-white">

        {{-- Identité --}}
        <section class="grid gap-6 p-6 md:grid-cols-[14rem_1fr]">
            <div>
                <h2 class="font-semibold">Identité</h2>
                <p class="mt-1 text-xs text-neutral-500">L'identifiant est attribué automatiquement à l'enregistrement.</p>
            </div>
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="prenom" class="mb-1.5 block text-[13px] font-medium">Prénom <span class="text-brand-600">*</span></label>
                    <input id="prenom" name="prenom" value="{{ old('prenom') }}" placeholder="Ex. Mariama" class="{{ $cls('prenom') }}">
                    @error('prenom')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="nom" class="mb-1.5 block text-[13px] font-medium">Nom <span class="text-brand-600">*</span></label>
                    <input id="nom" name="nom" value="{{ old('nom') }}" class="{{ $cls('nom') }}">
                    @error('nom')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <span class="mb-1.5 block text-[13px] font-medium">Public <span class="text-brand-600">*</span></span>
                    <div class="grid grid-cols-3 rounded-lg bg-neutral-100 p-1 text-sm">
                        @foreach (['femme' => 'Femme', 'homme' => 'Homme', 'enfant' => 'Enfant'] as $v => $l)
                            <label class="cursor-pointer">
                                <input type="radio" name="public" value="{{ $v }}" class="peer sr-only" {{ old('public', 'femme') === $v ? 'checked' : '' }}>
                                <span class="block rounded-md py-1.5 text-center text-neutral-500 peer-checked:bg-white peer-checked:font-medium peer-checked:text-neutral-900 peer-checked:shadow-sm peer-focus-visible:ring-2 peer-focus-visible:ring-brand-600/40">{{ $l }}</span>
                            </label>
                        @endforeach
                    </div>
                    <p class="mt-1.5 text-xs text-neutral-500">Détermine l'accès à l'espace femmes.</p>
                </div>
                <div>
                    <label for="date_naissance" class="mb-1.5 block text-[13px] font-medium">Date de naissance <span class="font-normal text-neutral-400">(facultatif)</span></label>
                    <input id="date_naissance" name="date_naissance" type="date" max="{{ now()->toDateString() }}" value="{{ old('date_naissance') }}" class="{{ $cls('date_naissance') }}">
                    <p class="mt-1.5 text-xs text-neutral-500">Permet l'offre d'anniversaire.</p>
                    @error('date_naissance')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="mb-1.5 block text-[13px] font-medium">Identifiant</label>
                    <input value="Généré automatiquement ({{ $nextCode }})" disabled class="{{ $inp }} border-neutral-200 bg-neutral-100 text-neutral-500">
                </div>
                <div>
                    <label class="mb-1.5 block text-[13px] font-medium">Date d'inscription</label>
                    <input value="{{ now()->format('d/m/Y') }}" disabled class="{{ $inp }} border-neutral-200 bg-neutral-100 text-neutral-500">
                </div>
            </div>
        </section>

        {{-- Coordonnées --}}
        <section class="grid gap-6 p-6 md:grid-cols-[14rem_1fr]">
            <div>
                <h2 class="font-semibold">Coordonnées</h2>
                <p class="mt-1 text-xs text-neutral-500">Format sénégalais : 9 chiffres (indicatif +221 ajouté automatiquement).</p>
            </div>
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="telephone" class="mb-1.5 block text-[13px] font-medium">Téléphone <span class="text-brand-600">*</span></label>
                    <div class="flex"><span class="flex items-center rounded-l-lg border border-r-0 border-neutral-200 bg-neutral-50 px-3 text-sm text-neutral-500">+221</span>
                    <input id="telephone" name="telephone" inputmode="numeric" autocomplete="tel-national" value="{{ old('telephone') }}" class="{{ $cls('telephone') }} rounded-l-none"></div>
                    @error('telephone')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="whatsapp" class="mb-1.5 block text-[13px] font-medium">WhatsApp <span class="text-brand-600">*</span></label>
                    <div class="flex"><span class="flex items-center rounded-l-lg border border-r-0 border-neutral-200 bg-neutral-50 px-3 text-sm text-neutral-500">+221</span>
                    <input id="whatsapp" name="whatsapp" inputmode="numeric" value="{{ old('whatsapp') }}" class="{{ $cls('whatsapp') }} rounded-l-none"></div>
                    @error('whatsapp')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                    <label class="mt-2 flex cursor-pointer items-center gap-2 text-[13px]">
                        <input type="checkbox" id="whatsapp_same" name="whatsapp_same" value="1" {{ old('whatsapp_same', old('_token') ? null : '1') ? 'checked' : '' }} class="h-4 w-4 rounded border-neutral-300 text-brand-600 focus:ring-brand-600/30">
                        Identique au téléphone
                    </label>
                </div>

                @if ($doublon)
                    <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900 sm:col-span-2">
                        <p><strong class="font-semibold">Doublon possible :</strong> ce téléphone appartient déjà à <strong>{{ $doublon['nom'] }}</strong> ({{ $doublon['code'] }}).</p>
                        <div class="mt-3 flex flex-wrap gap-2">
                            <a href="{{ url('/clients/'.$doublon['id']) }}" class="rounded-lg border border-amber-300 bg-white px-3 py-1.5 text-[13px] font-medium">Ouvrir la fiche existante</a>
                            <button type="submit" name="force" value="1" class="rounded-lg px-3 py-1.5 text-[13px] font-medium text-amber-900 hover:bg-amber-100">Créer quand même</button>
                        </div>
                    </div>
                @endif

                <div class="sm:col-span-2">
                    <label for="email" class="mb-1.5 block text-[13px] font-medium">Adresse e-mail <span class="font-normal text-neutral-400">(facultatif)</span></label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="nom@exemple.com" class="{{ $cls('email') }}">
                    @error('email')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </section>

        {{-- Communication --}}
        <section class="grid gap-6 p-6 md:grid-cols-[14rem_1fr]">
            <div>
                <h2 class="font-semibold">Communication</h2>
                <p class="mt-1 text-xs text-neutral-500">Le consentement est obligatoire pour recevoir des promotions. Les rappels de rendez-vous restent possibles.</p>
            </div>
            <div>
                <span class="mb-1.5 block text-[13px] font-medium">Canal préféré</span>
                <div class="grid gap-3 sm:grid-cols-3">
                    @foreach ([['whatsapp','WhatsApp','Messages préremplis'], ['appel','Appel','Téléphone'], ['sms','SMS','Hors application']] as [$v,$t,$d])
                        <label class="cursor-pointer">
                            <input type="radio" name="canal_prefere" value="{{ $v }}" class="peer sr-only" {{ old('canal_prefere', 'whatsapp') === $v ? 'checked' : '' }}>
                            <span class="block rounded-lg border border-neutral-200 p-3 text-sm peer-checked:border-brand-600 peer-checked:bg-brand-50 peer-checked:ring-1 peer-checked:ring-brand-600 peer-focus-visible:ring-2">
                                <strong class="block font-semibold">{{ $t }}</strong><span class="text-xs text-neutral-500">{{ $d }}</span>
                            </span>
                        </label>
                    @endforeach
                </div>
                <label class="mt-4 flex cursor-pointer gap-3 rounded-lg border border-neutral-200 p-3 has-[:checked]:border-brand-600 has-[:checked]:bg-brand-50">
                    <input type="checkbox" name="consent_promotions" value="1" {{ old('consent_promotions') ? 'checked' : '' }} class="mt-0.5 h-4 w-4 rounded border-neutral-300 text-brand-600 focus:ring-brand-600/30">
                    <span class="text-sm"><strong class="block font-semibold">La cliente accepte de recevoir des offres et des promotions</strong>
                    <span class="text-xs text-neutral-500">Consentement recueilli oralement le {{ now()->format('d/m/Y') }} par {{ auth()->user()->name }} — date et auteur enregistrés. Révocable à tout moment.</span></span>
                </label>
            </div>
        </section>

        {{-- Notes --}}
        <section class="grid gap-6 p-6 md:grid-cols-[14rem_1fr]">
            <div>
                <h2 class="font-semibold">Notes &amp; préférences</h2>
                <p class="mt-1 text-xs text-neutral-500">Visibles par l'équipe. N'y inscrivez pas de données de santé détaillées.</p>
            </div>
            <div class="space-y-5">
                <div>
                    <label for="notes" class="mb-1.5 block text-[13px] font-medium">Notes <span class="font-normal text-neutral-400">(facultatif)</span></label>
                    <textarea id="notes" name="notes" rows="4" class="{{ $cls('notes') }}">{{ old('notes') }}</textarea>
                </div>
                <div class="max-w-xs">
                    <label for="statut" class="mb-1.5 block text-[13px] font-medium">Statut</label>
                    <select id="statut" name="statut" class="{{ $cls('statut') }}">
                        <option value="actif" @selected(old('statut', 'actif') === 'actif')>Actif</option>
                        <option value="inactif" @selected(old('statut') === 'inactif')>Inactif</option>
                    </select>
                </div>
            </div>
        </section>
    </div>

    {{-- Actions du bas --}}
    <div class="mt-5 flex flex-wrap justify-end gap-2">
        <a href="{{ route('clients.index') }}" class="rounded-lg border border-neutral-300 bg-white px-4 py-2.5 text-sm font-medium hover:bg-neutral-50">Annuler</a>
        <button type="submit" class="rounded-lg bg-brand-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-700">Enregistrer le client</button>
    </div>
</form>

{{-- Message de confirmation --}}
@if (session('status'))
    <div role="status" class="fixed bottom-6 left-6 max-w-sm rounded-xl bg-neutral-900 px-5 py-4 text-sm text-white shadow-lg">
        <strong class="block font-semibold">{{ session('status')[0] }}</strong>
        <span class="text-neutral-300">{{ session('status')[1] }}</span>
    </div>
@endif

<script>
    // Espaces automatiques : 77 654 21 08
    const fmt = v => v.replace(/\D/g, '').slice(0, 9).replace(/^(\d{2})(\d{0,3})(\d{0,2})(\d{0,2}).*/, (_, a, b, c, d) => [a, b, c, d].filter(Boolean).join(' '));
    const tel = document.getElementById('telephone'), wa = document.getElementById('whatsapp'), same = document.getElementById('whatsapp_same');

    function sync() {
        wa.readOnly = same.checked;
        wa.classList.toggle('bg-neutral-50', same.checked);
        if (same.checked) wa.value = tel.value;
    }
    tel.addEventListener('input', () => { tel.value = fmt(tel.value); sync(); });
    wa.addEventListener('input', () => { wa.value = fmt(wa.value); });
    same.addEventListener('change', sync);
    tel.value = fmt(tel.value); wa.value = fmt(wa.value); sync();
</script>
@endsection