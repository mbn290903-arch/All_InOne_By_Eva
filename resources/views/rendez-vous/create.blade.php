@extends('layouts.app')

@section('title', 'Nouveau rendez-vous')
@section('page-title', 'Nouveau rendez-vous')

@section('content')

    <div class="max-w-5xl mx-auto">

        <div class="bg-white rounded-xl shadow-sm p-6">

            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-800">
                    Planifier un rendez-vous
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Renseignez les informations du rendez-vous.
                </p>
            </div>

            <form action="{{ route('rendez-vous.store') }}" method="POST">
                @csrf

                {{-- Informations du client et de l'employé --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">

                    <div>
                        <label class="block text-sm font-medium mb-2">
                            Client
                        </label>

                        <select name="client_id" class="w-full border rounded-lg p-3" required>
                            <option value="">Choisir un client</option>

                            @foreach ($clients as $client)
                                <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                    {{ $client->nomComplet }}
                                </option>
                            @endforeach
                        </select>

                        @error('client_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">
                            Employé
                        </label>

                        <select name="employe_id" class="w-full border rounded-lg p-3" required>
                            <option value="">Choisir un employé</option>

                            @foreach ($employes as $employe)
                                <option value="{{ $employe->id }}"
                                    {{ old('employe_id') == $employe->id ? 'selected' : '' }}>
                                    {{ $employe->nomComplet }}
                                </option>
                            @endforeach
                        </select>

                        @error('employe_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                {{-- Date et heure --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium mb-2">
                        Date et heure du rendez-vous
                    </label>

                    <input type="datetime-local" name="debut" value="{{ old('debut') }}"
                        @unless (auth()->user()->peutPlanifierDansLePasse())
                            min="{{ now()->format('Y-m-d\TH:i') }}"
                        @endunless
                        class="w-full border rounded-lg p-3" required>

                    @error('debut')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                {{-- Zone du salon --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium mb-2">
                        Zone du salon
                    </label>

                    <select name="zone" id="zone-select" class="w-full border rounded-lg p-3" required>
                        <option value="">Choisir une zone</option>

                        @foreach ($zones as $zone)
                            <option value="{{ $zone->value }}" {{ old('zone') === $zone->value ? 'selected' : '' }}>
                                {{ $zone->label() }}
                            </option>
                        @endforeach
                    </select>

                    @error('zone')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Services --}}
                <div class="mb-6">
                    <h3 class="font-semibold text-gray-800 mb-3">
                        Prestations
                    </h3>

                    <p class="text-sm text-gray-500 mb-3">
                        Sélectionnez un ou plusieurs services.
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">

                        @foreach ($services as $service)
                            @php
                                $zoneService =
                                    $service->zone instanceof \BackedEnum ? $service->zone->value : $service->zone;
                            @endphp
                            <label
                                class="item-prestation border rounded-lg p-4 flex items-start gap-3 cursor-pointer hover:bg-gray-50"
                                data-zone="{{ $zoneService }}">

                                <input type="checkbox" name="service_ids[]" value="{{ $service->id }}" class="mt-1"
                                    @checked(in_array($service->id, old('service_ids', [])))>

                                <div>
                                    <p class="font-medium">
                                        {{ $service->nom }}
                                    </p>

                                    <p class="text-sm text-gray-500">
                                        {{ number_format($service->prix, 0, ',', ' ') }} FCFA
                                        · {{ $service->duree_minutes }} min
                                    </p>
                                </div>

                            </label>
                        @endforeach

                    </div>

                    @error('service_ids')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                {{-- Forfaits --}}
                <div class="mb-6">
                    <h3 class="font-semibold text-gray-800 mb-3">
                        Forfaits
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">

                        @foreach ($forfaits as $forfait)
                            @php
                                $zonesForfait = $forfait->services
                                    ->map(fn($s) => $s->zone instanceof \BackedEnum ? $s->zone->value : $s->zone)
                                    ->unique();
                                $zoneForfait = $zonesForfait->count() === 1 ? $zonesForfait->first() : '';
                            @endphp
                            <label
                                class="item-prestation border rounded-lg p-4 flex items-start gap-3 cursor-pointer hover:bg-gray-50"
                                data-zone="{{ $zoneForfait }}">

                                <input type="checkbox" name="forfait_ids[]" value="{{ $forfait->id }}" class="mt-1"
                                    @checked(in_array($forfait->id, old('forfait_ids', [])))>

                                <div>
                                    <p class="font-medium">
                                        {{ $forfait->nom }}
                                    </p>

                                    <p class="text-sm text-gray-500">
                                        {{ number_format($forfait->prix, 0, ',', ' ') }} FCFA
                                        · {{ $forfait->dureeTotale() }} min
                                    </p>
                                </div>

                            </label>
                        @endforeach

                    </div>

                    @error('forfait_ids')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Commentaire --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium mb-2">
                        Commentaire (facultatif)
                    </label>

                    <textarea name="commentaire" rows="3" class="w-full border rounded-lg p-3"
                        placeholder="Ajouter une information...">{{ old('commentaire') }}</textarea>
                </div>

                {{-- Boutons --}}
                <div class="flex justify-end gap-3 border-t pt-5">

                    <a href="{{ route('rendez-vous.index') }}" class="px-5 py-2.5 border rounded-lg text-gray-700">
                        Annuler
                    </a>

                    <button type="submit" class="px-5 py-2.5 bg-[#6b2340] text-white rounded-lg">
                        Enregistrer le rendez-vous
                    </button>

                </div>

            </form>

        </div>

    </div>
    @push('scripts')
        <script>
            const zoneSelect = document.getElementById('zone-select');

            function filtrerParZone() {
                const zone = zoneSelect.value;

                document.querySelectorAll('.item-prestation').forEach(item => {
                    const visible = zone === '' || item.dataset.zone === zone;

                    item.style.display = visible ? '' : 'none';

                    // Décocher ce qui n'est plus affiché
                    if (!visible) {
                        const caseACocher = item.querySelector('input[type="checkbox"]');
                        if (caseACocher) caseACocher.checked = false;
                    }
                });
            }

            zoneSelect.addEventListener('change', filtrerParZone);
            filtrerParZone(); // garde le filtre après une erreur de validation
        </script>
    @endpush

@endsection
