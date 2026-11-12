@extends('layouts.app')
@section('title', 'Nouvel encaissement — All in One by Eva')
@section('breadcrumb')
    <a href="{{ route('caisse.index') }}" class="hover:underline">
        Caisse
    </a>
    / Nouvel encaissement
@endsection
@section('content')
    <div class="mx-auto max-w-4xl space-y-6">

        {{-- En-tête --}}
        <div>
            <a href="{{ route('caisse.index') }}"
                class="mb-3 inline-flex items-center gap-2 text-sm text-gray-500 hover:text-[#76243D]">
                <i data-lucide="arrow-left" class="h-4 w-4"></i>
                Retour à la caisse
            </a>

            <h1 class="font-serif text-2xl font-bold text-[#76243D]">
                Nouvel encaissement
            </h1>

            <p class="mt-1 text-sm text-gray-500">
                Enregistrez un paiement dans la caisse du salon.
            </p>
        </div>

        {{-- Erreurs --}}
        @if ($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                <p class="mb-2 font-semibold">
                    Veuillez corriger les erreurs suivantes :
                </p>

                <ul class="list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('caisse.store') }}" method="POST" class="space-y-6">

            @csrf

            {{-- Informations du client --}}
            <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">

                <div class="mb-5 flex items-center gap-3">
                    <div class="rounded-xl bg-[#f8edf1] p-2.5 text-[#76243D]">
                        <i data-lucide="user-round" class="h-5 w-5"></i>
                    </div>

                    <div>
                        <h2 class="font-semibold text-gray-800">
                            Informations du client
                        </h2>
                        <p class="text-xs text-gray-400">
                            Sélectionnez le client concerné.
                        </p>
                    </div>
                </div>

                @if ($rendezVous)

                    <input type="hidden" name="client_id" value="{{ $rendezVous->client_id }}">

                    <input type="hidden" name="rendez_vous_id" value="{{ $rendezVous->id }}">

                    <div class="rounded-xl border border-[#76243D]/10 bg-[#faf6f7] p-4">

                        <p class="mb-2 text-xs font-medium uppercase text-gray-500">
                            Client sélectionné
                        </p>

                        <p class="font-semibold text-gray-800">
                            {{ $rendezVous->client->prenom }}
                            {{ $rendezVous->client->nom }}
                        </p>

                        <p class="mt-1 text-sm text-gray-500">
                            {{ $rendezVous->client->telephone ?? 'Téléphone non renseigné' }}
                        </p>

                        <p class="mt-3 text-xs text-[#76243D]">
                            Rendez-vous n° {{ $rendezVous->id }}
                            @if ($rendezVous->debut)
                                · {{ $rendezVous->debut->format('d/m/Y à H:i') }}
                            @endif
                        </p>

                    </div>
                @else
                    <div>
                        <label for="client_id" class="mb-2 block text-sm font-medium text-gray-700">
                            Client <span class="text-red-500">*</span>
                        </label>

                        <select name="client_id" id="client_id" required
                            class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none focus:border-[#76243D] focus:ring-2 focus:ring-[#76243D]/10">

                            <option value="">Choisir un client</option>

                            @foreach ($clients as $client)
                                <option value="{{ $client->id }}" @selected(old('client_id') == $client->id)>
                                    {{ $client->prenom }} {{ $client->nom }}
                                    — {{ $client->telephone }}
                                </option>
                            @endforeach

                        </select>

                        @error('client_id')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                @endif

            </div>

            {{-- Informations du paiement --}}
            <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">

                <div class="mb-5 flex items-center gap-3">
                    <div class="rounded-xl bg-green-50 p-2.5 text-green-600">
                        <i data-lucide="wallet" class="h-5 w-5"></i>
                    </div>

                    <div>
                        <h2 class="font-semibold text-gray-800">
                            Détails du paiement
                        </h2>
                        <p class="text-xs text-gray-400">
                            Renseignez les informations de l'encaissement.
                        </p>
                    </div>
                </div>

                @if ($rendezVous)
                    <div class="mb-5 grid grid-cols-1 gap-4 sm:grid-cols-2">

                        <div class="rounded-xl bg-gray-50 p-4">
                            <p class="text-xs text-gray-500">Montant prévu</p>
                            <p class="mt-2 text-xl font-bold text-gray-800">
                                {{ number_format($rendezVous->prix_prevu, 0, ',', ' ') }}
                                FCFA
                            </p>
                        </div>

                        <div class="rounded-xl bg-amber-50 p-4">
                            <p class="text-xs text-amber-700">Reste à payer</p>
                            <p class="mt-2 text-xl font-bold text-amber-800">
                                {{ number_format($rendezVous->resteAPayer(), 0, ',', ' ') }}
                                FCFA
                            </p>
                        </div>

                    </div>
                @endif

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                    {{-- Mode de paiement --}}
                    <div>
                        <label for="mode_paiement_id" class="mb-2 block text-sm font-medium text-gray-700">
                            Mode de paiement <span class="text-red-500">*</span>
                        </label>

                        <select name="mode_paiement_id" id="mode_paiement_id" required
                            class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none focus:border-[#76243D]">

                            <option value="">Choisir un mode</option>

                            @foreach ($modes as $mode)
                                <option value="{{ $mode->id }}" @selected(old('mode_paiement_id') == $mode->id)>
                                    {{ $mode->libelle }}
                                </option>
                            @endforeach

                        </select>

                        @error('mode_paiement_id')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Montant --}}
                    <div>
                        <label for="montant_paye" class="mb-2 block text-sm font-medium text-gray-700">
                            Montant encaissé (FCFA)
                            <span class="text-red-500">*</span>
                        </label>

                        <input type="number" name="montant_paye" id="montant_paye" min="1"
                            @if ($rendezVous) max="{{ $rendezVous->resteAPayer() }}" @endif step="1"
                            value="{{ old('montant_paye') }}" required placeholder="Ex : 15000"
                            class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm outline-none focus:border-[#76243D] focus:ring-2 focus:ring-[#76243D]/10">

                        @error('montant_paye')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Référence --}}
                    <div class="sm:col-span-2">
                        <label for="reference" class="mb-2 block text-sm font-medium text-gray-700">
                            Référence du paiement
                            <span class="text-xs font-normal text-gray-400">
                                (Facultatif)
                            </span>
                        </label>

                        <input type="text" name="reference" id="reference" value="{{ old('reference') }}"
                            maxlength="255" placeholder="Ex : Référence Wave, Orange Money..."
                            class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm outline-none focus:border-[#76243D]">

                        @error('reference')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

            </div>

            {{-- Informations --}}
            <div class="flex gap-3 rounded-xl border border-blue-100 bg-blue-50 p-4 text-sm text-blue-800">
                <i data-lucide="info" class="mt-0.5 h-5 w-5 shrink-0"></i>

                <p>
                    L'encaissement sera enregistré avec la date et l'heure actuelles.
                    Les paiements associés à un rendez-vous ne peuvent pas dépasser
                    le reste à payer.
                </p>
            </div>

            {{-- Boutons --}}
            <div class="flex flex-col-reverse justify-end gap-3 sm:flex-row">

                <a href="{{ route('caisse.index') }}"
                    class="rounded-xl border border-gray-200 px-6 py-3 text-center text-sm font-medium text-gray-600 hover:bg-gray-50">
                    Annuler
                </a>

                <button type="submit"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#76243D] px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#5d1c30]">

                    <i data-lucide="check-circle" class="h-4 w-4"></i>
                    Enregistrer le paiement

                </button>

            </div>

        </form>

    </div>

@endsection
