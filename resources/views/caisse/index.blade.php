@extends('layouts.app')

@section('title', 'Caisse — All in One by Eva')
@section('breadcrumb', 'Caisse')

@section('content')

    <div class="space-y-6">

        {{-- En-tête --}}
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <h1 class="font-serif text-2xl font-bold text-[#76243D]">
                    Gestion de la caisse
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Suivez les encaissements et les recettes du salon.
                </p>
            </div>

            <a href="{{ route('caisse.create') }}"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#76243D] px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#5d1c30]">
                <i data-lucide="plus" class="h-4 w-4"></i>
                Nouvel encaissement
            </a>
        </div>

        {{-- Messages --}}
        @if (session('success'))
            <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ session('error') }}
            </div>
        @endif

        {{-- Erreurs --}}
        @if ($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                <ul class="list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Statistiques --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">

            <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">Recettes du jour</span>
                    <div class="rounded-xl bg-green-50 p-2 text-green-600">
                        <i data-lucide="wallet" class="h-5 w-5"></i>
                    </div>
                </div>

                <p class="mt-4 text-2xl font-bold text-gray-800">
                    {{ number_format($recettesJour, 0, ',', ' ') }} FCFA
                </p>

                <p class="mt-1 text-xs text-gray-400">
                    Encaissements valides aujourd'hui
                </p>
            </div>

            <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">Recettes du mois</span>
                    <div class="rounded-xl bg-[#f8edf1] p-2 text-[#76243D]">
                        <i data-lucide="chart-no-axes-column-increasing" class="h-5 w-5"></i>
                    </div>
                </div>

                <p class="mt-4 text-2xl font-bold text-gray-800">
                    {{ number_format($recettesMois, 0, ',', ' ') }} FCFA
                </p>

                <p class="mt-1 text-xs text-gray-400">
                    Total encaissé ce mois
                </p>
            </div>

            <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">Paiements du jour</span>
                    <div class="rounded-xl bg-blue-50 p-2 text-blue-600">
                        <i data-lucide="receipt" class="h-5 w-5"></i>
                    </div>
                </div>

                <p class="mt-4 text-2xl font-bold text-gray-800">
                    {{ $nbPaiementsJour }}
                </p>

                <p class="mt-1 text-xs text-gray-400">
                    Nombre d'encaissements valides
                </p>
            </div>

        </div>

        {{-- Filtres --}}
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">

            <div class="mb-4 flex items-center gap-2">
                <i data-lucide="filter" class="h-4 w-4 text-[#76243D]"></i>
                <h2 class="font-semibold text-gray-800">Filtrer les paiements</h2>
            </div>

            <form action="{{ route('caisse.index') }}" method="GET"
                class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">

                <div class="xl:col-span-2">
                    <label class="mb-1.5 block text-xs font-medium text-gray-600">
                        Rechercher un client
                    </label>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Nom ou prénom..."
                        class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm outline-none focus:border-[#76243D] focus:ring-2 focus:ring-[#76243D]/10">
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-medium text-gray-600">
                        Du
                    </label>
                    <input type="date" name="du" value="{{ request('du') }}"
                        class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm outline-none focus:border-[#76243D]">
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-medium text-gray-600">
                        Au
                    </label>
                    <input type="date" name="au" value="{{ request('au') }}"
                        class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm outline-none focus:border-[#76243D]">
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-medium text-gray-600">
                        Mode de paiement
                    </label>
                    <select name="mode"
                        class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm outline-none focus:border-[#76243D]">
                        <option value="">Tous les modes</option>
                        @foreach ($modes as $mode)
                            <option value="{{ $mode->id }}" @selected(request('mode') == $mode->id)>
                                {{ $mode->libelle }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-medium text-gray-600">
                        Statut
                    </label>
                    <select name="statut"
                        class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm outline-none focus:border-[#76243D]">
                        <option value="valides" @selected(request('statut', 'valides') === 'valides')>
                            Valides
                        </option>
                        <option value="annules" @selected(request('statut') === 'annules')>
                            Annulés
                        </option>
                        <option value="tous" @selected(request('statut') === 'tous')>
                            Tous
                        </option>
                    </select>
                </div>

                <div class="flex items-end gap-2 sm:col-span-2 lg:col-span-3 xl:col-span-6">
                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-[#76243D] px-5 py-2.5 text-sm font-medium text-white hover:bg-[#5d1c30]">
                        <i data-lucide="search" class="h-4 w-4"></i>
                        Filtrer
                    </button>

                    <a href="{{ route('caisse.index') }}"
                        class="rounded-xl border border-gray-200 px-5 py-2.5 text-sm text-gray-600 hover:bg-gray-50">
                        Réinitialiser
                    </a>
                </div>

            </form>
        </div>

        {{-- Tableau des paiements --}}
        <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">

            <div class="flex flex-col justify-between gap-2 border-b border-gray-100 p-5 sm:flex-row sm:items-center">
                <div>
                    <h2 class="font-semibold text-gray-800">Historique des paiements</h2>
                    <p class="mt-1 text-xs text-gray-400">
                        {{ $paiements->total() }} paiement(s) trouvé(s)
                    </p>
                </div>
            </div>

            <div class="overflow-x-auto">

                <table class="w-full text-left text-sm">

                    <thead class="bg-[#faf8f7] text-xs uppercase text-gray-500">
                        <tr>
                            <th class="px-5 py-4 font-semibold">Référence</th>
                            <th class="px-5 py-4 font-semibold">Client</th>
                            <th class="px-5 py-4 font-semibold">Rendez-vous</th>
                            <th class="px-5 py-4 font-semibold">Mode</th>
                            <th class="px-5 py-4 font-semibold">Montant payé</th>
                            <th class="px-5 py-4 font-semibold">Reste</th>
                            <th class="px-5 py-4 font-semibold">Date</th>
                            <th class="px-5 py-4 font-semibold">Statut</th>
                            <th class="px-5 py-4 text-right font-semibold">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">

                        @forelse($paiements as $paiement)
                            <tr class="transition hover:bg-gray-50/70">

                                <td class="whitespace-nowrap px-5 py-4">
                                    <span class="font-medium text-[#76243D]">
                                        {{ $paiement->reference ?: '#' . $paiement->id }}
                                    </span>
                                </td>

                                <td class="whitespace-nowrap px-5 py-4">
                                    <div class="font-medium text-gray-800">
                                        {{ $paiement->client->prenom ?? '' }}
                                        {{ $paiement->client->nom ?? 'Client inconnu' }}
                                    </div>
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-gray-600">
                                    @if ($paiement->rendezVous)
                                        <span class="font-medium">
                                            RDV #{{ $paiement->rendezVous->id }}
                                        </span>
                                        <div class="text-xs text-gray-400">
                                            {{ $paiement->rendezVous->debut?->format('d/m/Y H:i') }}
                                        </div>
                                    @else
                                        <span class="text-gray-400">Sans rendez-vous</span>
                                    @endif
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-gray-600">
                                    {{ $paiement->mode->libelle ?? 'Non renseigné' }}
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 font-semibold text-gray-800">
                                    {{ number_format($paiement->montant_paye, 0, ',', ' ') }} FCFA
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-gray-600">
                                    {{ number_format($paiement->reste_a_payer, 0, ',', ' ') }} FCFA
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-gray-600">
                                    {{ $paiement->paye_le?->format('d/m/Y H:i') }}
                                </td>

                                <td class="whitespace-nowrap px-5 py-4">
                                    @if ($paiement->estAnnule())
                                        <span class="rounded-full bg-red-50 px-3 py-1 text-xs font-medium text-red-600">
                                            Annulé
                                        </span>
                                    @elseif($paiement->reste_a_payer > 0)
                                        <span
                                            class="rounded-full bg-amber-50 px-3 py-1 text-xs font-medium text-amber-700">
                                            Partiel
                                        </span>
                                    @else
                                        <span
                                            class="rounded-full bg-green-50 px-3 py-1 text-xs font-medium text-green-700">
                                            Soldé
                                        </span>
                                    @endif
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-right">

                                    @if (!$paiement->estAnnule())
                                        <details class="relative inline-block text-left">

                                            <summary
                                                class="inline-flex cursor-pointer list-none items-center gap-1 rounded-lg border border-red-100 px-3 py-2 text-xs font-medium text-red-600 hover:bg-red-50">
                                                <i data-lucide="ban" class="h-3.5 w-3.5"></i>
                                                Annuler
                                            </summary>

                                            <div
                                                class="absolute right-0 z-20 mt-2 w-64 rounded-xl border border-gray-100 bg-white p-4 text-left shadow-xl">

                                                <p class="mb-3 text-sm font-semibold text-gray-800">
                                                    Annuler ce paiement
                                                </p>

                                                <form action="{{ route('caisse.annuler', $paiement) }}" method="POST"
                                                    onsubmit="return confirm('Confirmer l’annulation de ce paiement ?')">

                                                    @csrf
                                                    @method('PATCH')

                                                    <label class="mb-1 block text-xs text-gray-600">
                                                        Motif d'annulation
                                                    </label>

                                                    <textarea name="motif_annulation" required minlength="5" maxlength="255" rows="3"
                                                        placeholder="Expliquez le motif..."
                                                        class="w-full rounded-lg border border-gray-200 p-2 text-xs outline-none focus:border-[#76243D]"></textarea>

                                                    <button type="submit"
                                                        class="mt-3 w-full rounded-lg bg-red-600 px-3 py-2 text-xs font-semibold text-white hover:bg-red-700">
                                                        Confirmer l'annulation
                                                    </button>

                                                </form>
                                            </div>

                                        </details>
                                    @else
                                        <span class="text-xs text-gray-400">
                                            Annulation effectuée
                                        </span>
                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="9" class="px-5 py-12 text-center">
                                    <div class="mx-auto flex max-w-sm flex-col items-center">
                                        <div class="mb-3 rounded-full bg-gray-100 p-4 text-gray-400">
                                            <i data-lucide="receipt" class="h-7 w-7"></i>
                                        </div>

                                        <p class="font-medium text-gray-700">
                                            Aucun paiement trouvé
                                        </p>

                                        <p class="mt-1 text-xs text-gray-400">
                                            Les encaissements apparaîtront ici.
                                        </p>

                                        <a href="{{ route('caisse.create') }}"
                                            class="mt-4 text-sm font-semibold text-[#76243D] hover:underline">
                                            Effectuer un encaissement
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

            {{-- Pagination --}}
            @push('styles')
                <style>
                    nav[role="navigation"] a {
                        color: #76243D !important;
                        border-color: #EAE5E2 !important;
                        background-color: #FFFFFF !important;
                        border-radius: 7px !important;
                        margin: 0 5px;
                        transition: all .2s ease;
                    }

                    nav[role="navigation"] a:hover {
                        background-color: #F6E9ED !important;
                        color: #5D1830 !important;
                        border-color: #76243D !important;
                    }

                    nav[role="navigation"] span[aria-current="page"] span {
                        background-color: #76243D !important;
                        border-color: #76243D !important;
                        color: #FFFFFF !important;
                        font-weight: 600;
                        border-radius: 7px !important;
                    }

                    nav[role="navigation"] span.cursor-default {
                        color: #AAA2A5 !important;
                    }
                </style>
            @endpush

            @if ($paiements->hasPages())
                <div class="border-t border-gray-100 p-5">
                    {{ $paiements->links() }}
                </div>
            @endif

        </div>

    </div>

@endsection
