
@extends('layouts.app')

@section('header', 'Catalogue des prestations')

@section('content')
<div class="min-h-screen bg-[#F7F4F1] p-5 md:p-6 space-y-8 text-[#292326]">

    <div>
        <h1 class="text-2xl font-semibold tracking-tight">
            Catalogue des prestations
        </h1>

        <p class="text-sm text-[#827980] mt-2">
            Consultez les prestations, leurs tarifs et les forfaits du salon.
        </p>

        <div class="flex flex-wrap gap-3 mt-4">
            <span class="px-3 py-2 bg-white border border-[#E9E2DE] rounded-lg text-sm">
                {{ $total }} prestations
            </span>

            <span class="px-3 py-2 bg-white border border-[#E9E2DE] rounded-lg text-sm">
                {{ $forfaits->count() }} forfaits
            </span>

            <span class="px-3 py-2 bg-white border border-[#E9E2DE] rounded-lg text-sm">
                {{ $categories->count() }} catégories
            </span>
        </div>
    </div>

    {{-- CATÉGORIES --}}
    <section class="bg-white border border-[#E9E2DE] rounded-xl p-5">
        <h2 class="text-lg font-semibold mb-4">Catégories</h2>

        <div class="flex flex-wrap gap-2">
            @forelse($categories as $categorie)
                <span class="px-3 py-2 rounded-lg bg-[#F7F4F1] text-sm">
                    {{ $categorie->nom }}
                    <span class="text-[#827980]">
                        ({{ $categorie->services_count }})
                    </span>
                </span>
            @empty
                <p class="text-sm text-[#827980]">
                    Aucune catégorie disponible.
                </p>
            @endforelse
        </div>
    </section>

    {{-- PRESTATIONS --}}
    <section class="space-y-5">
        <h2 class="text-lg font-semibold">Prestations et tarifs</h2>

        @forelse($groupes as $categorie => $services)
            <div class="bg-white border border-[#E9E2DE] rounded-xl overflow-hidden">

                <div class="px-5 py-4 bg-[#F5E9EE] border-b border-[#E9E2DE]">
                    <h3 class="font-semibold text-[#6B1F3A]">
                        {{ $categorie }}
                    </h3>
                </div>

                <div class="divide-y divide-[#F0EBE8]">
                    @foreach($services as $service)
                        <div class="p-5 flex flex-col md:flex-row md:items-center md:justify-between gap-3">

                            <div>
                                <h4 class="font-medium">
                                    {{ $service->nom }}
                                </h4>

                                @if(!empty($service->description))
                                    <p class="text-sm text-[#827980] mt-1">
                                        {{ $service->description }}
                                    </p>
                                @endif

                                <div class="flex flex-wrap gap-2 mt-2 text-xs text-[#827980]">
                                    <span>{{ $service->duree_format }}</span>

                                    <span>•</span>

                                    <span>
                                        {{ $service->zone === 'femmes' ? 'Espace femmes' : 'Zone mixte' }}
                                    </span>

                                    <span>•</span>

                                    <span>{{ $service->public }}</span>
                                </div>
                            </div>

                            <div class="font-semibold text-[#6B1F3A] whitespace-nowrap">
                                {{ $service->prix_format }}
                            </div>

                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="bg-white border border-[#E9E2DE] rounded-xl p-6 text-center text-sm text-[#827980]">
                Aucune prestation disponible.
            </div>
        @endforelse
    </section>

    {{-- FORFAITS --}}
    <section class="space-y-5" id="forfaits">
        <h2 class="text-lg font-semibold">Forfaits</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse($forfaits as $forfait)
                <div class="bg-white border border-[#E9E2DE] rounded-xl p-5 space-y-3">

                    <div class="flex justify-between items-start gap-3">
                        <h3 class="font-semibold">
                            {{ $forfait->nom }}
                        </h3>

                        @if($forfait->reduction > 0)
                            <span class="px-2 py-1 rounded-full bg-[#F5E9EE] text-[#6B1F3A] text-xs">
                                -{{ $forfait->reduction }} %
                            </span>
                        @endif
                    </div>

                    <div>
                        <p class="text-xs text-[#827980] mb-1">
                            Prix du forfait
                        </p>

                        <p class="text-xl font-semibold text-[#6B1F3A]">
                            {{ number_format($forfait->prix, 0, ',', ' ') }} FCFA
                        </p>
                    </div>

                    <div class="text-sm text-[#827980]">
                        <p>
                            Durée : {{ $forfait->duree_format }}
                        </p>

                        <p class="mt-1">
                            Zone :
                            {{ $forfait->zone === 'femmes' ? 'Espace femmes' : 'Zone mixte' }}
                        </p>
                    </div>

                    <div class="border-t border-[#F0EBE8] pt-3">
                        <p class="text-xs font-semibold mb-2">
                            Prestations incluses
                        </p>

                        <ul class="text-sm text-[#827980] space-y-1">
                            @forelse($forfait->lignes as $ligne)
                                <li>• {{ $ligne }}</li>
                            @empty
                                <li>Aucune prestation renseignée.</li>
                            @endforelse
                        </ul>
                    </div>

                </div>
            @empty
                <div class="bg-white border border-[#E9E2DE] rounded-xl p-6 text-sm text-[#827980]">
                    Aucun forfait disponible.
                </div>
            @endforelse
        </div>
    </section>

    <p class="text-xs text-[#827980] text-center">
        Catalogue en consultation — les modifications sont réservées à l'administration.
    </p>

</div>
@endsection
