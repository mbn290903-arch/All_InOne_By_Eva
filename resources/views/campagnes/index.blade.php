
@extends('layouts.app')

@section('title', 'Campagnes WhatsApp — All in One by Eva')
@section('page-title', 'Campagnes WhatsApp')

@section('content')
<div class="space-y-6">

    {{-- En-tête --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-semibold">Campagnes WhatsApp</h1>
            <p class="mt-1 text-sm text-neutral-500">
                Prépare tes messages et suis les échanges avec tes clientes.
            </p>
        </div>
        <div class="rounded-xl bg-green-50 px-4 py-3 text-sm text-green-800">
            <i data-lucide="message-circle" class="mr-1 inline h-4 w-4"></i>
            Aucun envoi automatique
        </div>
    </div>

    {{-- Messages --}}
    @if (session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <p class="font-semibold">Une erreur est survenue :</p>
            <ul class="mt-2 list-inside list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Filtres --}}
    <section class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm">
        <div class="mb-5">
            <h2 class="font-semibold">1. Sélectionner les destinataires</h2>
            <p class="mt-1 text-sm text-neutral-500">
                Choisis un modèle puis applique les filtres souhaités.
            </p>
        </div>

        <form method="GET" action="{{ route('campagnes.index') }}" class="space-y-5">

            <div>
                <label for="modele_message_id" class="mb-1.5 block text-sm font-medium">
                    Modèle de message
                </label>
                <select id="modele_message_id" name="modele_message_id"
                    class="w-full rounded-lg border border-neutral-300 px-3 py-2.5 text-sm focus:border-brand-600 focus:outline-none">
                    <option value="">Choisir un modèle</option>
                    @foreach ($modeles as $item)
                        <option value="{{ $item->id }}"
                            @selected(request('modele_message_id') == $item->id)>
                            {{ $item->titre }}
                            ({{ $item->type instanceof \App\Enums\TypeMessage ? $item->type->label() : $item->type }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid gap-4 md:grid-cols-3">

                <div>
                    <label for="public" class="mb-1.5 block text-sm font-medium">Public</label>
                    <select id="public" name="public"
                        class="w-full rounded-lg border border-neutral-300 px-3 py-2.5 text-sm">
                        <option value="">Tous les publics</option>
                        <option value="femme" @selected(request('public') === 'femme')>Femme</option>
                        <option value="homme" @selected(request('public') === 'homme')>Homme</option>
                        <option value="enfant" @selected(request('public') === 'enfant')>Enfant</option>
                    </select>
                </div>

                <div>
                    <label for="statut" class="mb-1.5 block text-sm font-medium">Statut du client</label>
                    <select id="statut" name="statut"
                        class="w-full rounded-lg border border-neutral-300 px-3 py-2.5 text-sm">
                        <option value="">Tous les statuts</option>
                        <option value="actif" @selected(request('statut') === 'actif')>Actif</option>
                        <option value="inactif" @selected(request('statut') === 'inactif')>Inactif</option>
                    </select>
                </div>

                <div>
                    <label for="canal_prefere" class="mb-1.5 block text-sm font-medium">Canal préféré</label>
                    <select id="canal_prefere" name="canal_prefere"
                        class="w-full rounded-lg border border-neutral-300 px-3 py-2.5 text-sm">
                        <option value="">Tous les canaux</option>
                        <option value="whatsapp" @selected(request('canal_prefere') === 'whatsapp')>WhatsApp</option>
                        <option value="appel" @selected(request('canal_prefere') === 'appel')>Appel</option>
                        <option value="sms" @selected(request('canal_prefere') === 'sms')>SMS</option>
                    </select>
                </div>

            </div>

            <div class="grid gap-4 md:grid-cols-2">

                <div>
                    <label for="consentement" class="mb-1.5 block text-sm font-medium">
                        Consentement aux promotions
                    </label>
                    <select id="consentement" name="consentement"
                        class="w-full rounded-lg border border-neutral-300 px-3 py-2.5 text-sm">
                        <option value="">Tous (selon le type de campagne)</option>
                        <option value="oui" @selected(request('consentement') === 'oui')>Consentement donné</option>
                        <option value="non" @selected(request('consentement') === 'non')>Sans consentement</option>
                    </select>
                </div>

                <div>
                    <label for="inactifs_jours" class="mb-1.5 block text-sm font-medium">
                        Clients sans visite récente
                    </label>
                    <select id="inactifs_jours" name="inactifs_jours"
                        class="w-full rounded-lg border border-neutral-300 px-3 py-2.5 text-sm">
                        <option value="">Aucune limite</option>
                        <option value="30" @selected(request('inactifs_jours') == '30')>Aucune visite depuis 30 jours</option>
                        <option value="60" @selected(request('inactifs_jours') == '60')>Aucune visite depuis 60 jours</option>
                        <option value="90" @selected(request('inactifs_jours') == '90')>Aucune visite depuis 90 jours</option>
                        <option value="180" @selected(request('inactifs_jours') == '180')>Aucune visite depuis 6 mois</option>
                    </select>
                </div>

            </div>

            <label class="flex items-center gap-3 text-sm">
                <input type="checkbox" name="anniversaires_mois" value="1"
                    @checked(request()->boolean('anniversaires_mois'))
                    class="h-4 w-4 rounded border-neutral-300 text-brand-600">
                Anniversaires du mois en cours
            </label>

            <div class="flex flex-wrap gap-3">
                <button type="submit"
                    class="rounded-lg bg-[#292327] px-5 py-2.5 text-sm font-medium text-white hover:opacity-90">
                    <i data-lucide="filter" class="mr-1 inline h-4 w-4"></i>
                    Afficher les destinataires
                </button>

                <a href="{{ route('campagnes.index') }}"
                    class="rounded-lg border border-neutral-300 px-5 py-2.5 text-sm hover:bg-neutral-50">
                    Réinitialiser
                </a>
            </div>
        </form>
    </section>

    {{-- Aperçu --}}
    @if ($modele)
        <section class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm">
            <div class="mb-5">
                <h2 class="font-semibold">2. Aperçu des destinataires</h2>
                <p class="mt-1 text-sm text-neutral-500">
                    Modèle sélectionné : {{ $modele->titre }}
                </p>
            </div>

            <div class="mb-5 grid gap-3 sm:grid-cols-3">
                <div class="rounded-lg bg-neutral-50 p-4">
                    <p class="text-xs text-neutral-500">Clients correspondants</p>
                    <p class="mt-1 text-2xl font-semibold">{{ $clients->count() }}</p>
                </div>
                <div class="rounded-lg bg-green-50 p-4">
                    <p class="text-xs text-green-700">Numéros valides</p>
                    <p class="mt-1 text-2xl font-semibold text-green-800">{{ $valides->count() }}</p>
                </div>
                <div class="rounded-lg bg-red-50 p-4">
                    <p class="text-xs text-red-700">Numéros invalides ou absents</p>
                    <p class="mt-1 text-2xl font-semibold text-red-800">{{ $invalides }}</p>
                </div>
            </div>

            @if (in_array(
                $modele->type instanceof \App\Enums\TypeMessage ? $modele->type->value : $modele->type,
                ['promotion', 'fidelisation'],
                true
            ))
                <p class="mb-4 rounded-lg bg-amber-50 p-3 text-sm text-amber-800">
                    Seuls les clients ayant accepté les offres et promotions sont inclus dans cette campagne.
                </p>
            @endif

            @if ($valides->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b bg-neutral-50 text-xs uppercase text-neutral-500">
                            <tr>
                                <th class="px-3 py-3">Client</th>
                                <th class="px-3 py-3">WhatsApp</th>
                                <th class="px-3 py-3">Message personnalisé</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100">
                            @foreach ($valides as $client)
                                <tr>
                                    <td class="px-3 py-3 font-medium">
                                        {{ $client->prenom }} {{ $client->nom }}
                                    </td>
                                    <td class="px-3 py-3">{{ $client->numero_normalise }}</td>
                                    <td class="max-w-md px-3 py-3 text-neutral-600">
                                        {{ $client->message_apercu }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Création de la campagne --}}
                <form method="POST" action="{{ route('campagnes.store') }}" class="mt-6 space-y-4">
                    @csrf

                    <input type="hidden" name="modele_message_id" value="{{ $modele->id }}">

                    @foreach (['public', 'statut', 'canal_prefere', 'consentement', 'inactifs_jours'] as $filtre)
                        @if (request()->filled($filtre))
                            <input type="hidden" name="{{ $filtre }}" value="{{ request($filtre) }}">
                        @endif
                    @endforeach

                    @if (request()->boolean('anniversaires_mois'))
                        <input type="hidden" name="anniversaires_mois" value="1">
                    @endif

                    <div>
                        <label for="titre" class="mb-1.5 block text-sm font-medium">
                            Nom de la campagne
                        </label>
                        <input id="titre" name="titre" type="text" required maxlength="150"
                            value="{{ old('titre') }}"
                            placeholder="Ex. Offre spéciale de novembre"
                            class="w-full rounded-lg border border-neutral-300 px-3 py-2.5 text-sm">
                    </div>

                    <button type="submit"
                        class="rounded-lg bg-[#292327] px-5 py-3 text-sm font-medium text-white hover:opacity-90">
                        <i data-lucide="clipboard-check" class="mr-1 inline h-4 w-4"></i>
                        Préparer la campagne
                    </button>
                </form>
            @else
                <p class="rounded-lg bg-neutral-50 p-4 text-sm text-neutral-600">
                    Aucun numéro WhatsApp valide ne correspond à ces critères.
                    Modifie les filtres pour obtenir des destinataires.
                </p>
            @endif
        </section>
    @endif

</div>
@endsection
