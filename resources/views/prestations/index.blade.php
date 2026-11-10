@extends('layouts.app')

@section('header', 'Prestations & forfaits')

@section('content')

    <div class="min-h-screen bg-[#F7F4F1] p-5 md:p-6 space-y-8 text-[#292326]">

        @if (session('success'))
            <div class="bg-[#E6F2EC] text-[#438365] border border-[#CDE5D7] rounded-lg p-3 text-xs">

                {{ session('success') }}

            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-50 text-red-700 border border-red-200 rounded-lg p-3 text-xs">

                {{ session('error') }}

            </div>
        @endif

        @if ($errors->any())

            <div class="bg-red-50 border border-red-200 rounded-lg p-3">

                <p class="text-xs font-semibold text-red-700 mb-2">Veuillez corriger les erreurs suivantes :</p>

                <ul class="text-xs text-red-600 space-y-1">

                    @foreach ($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach

                </ul>

            </div>

        @endif



        {{-- EN-TÊTE --}}

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">

            <div>

                <h1 class="text-2xl font-semibold tracking-tight">

                    Prestations &amp; forfaits

                </h1>

                <p class="text-xs text-[#827980] mt-1">

                    {{ $total }} prestations ·

                    {{ $forfaits->count() }} forfaits ·

                    {{ $categories->count() }} catégories

                    <span class="ml-1 px-2 py-0.5 rounded-full bg-[#F5E9EE] text-[#6B1F3A]">

                        Démo

                    </span>

                </p>

            </div>



            <div class="flex gap-2">

                <a href="#forfaits"
                    class="px-4 py-2 bg-white border border-[#E9E2DE] rounded-lg text-sm hover:bg-[#FAF7F5]">

                    ◈ Nouveau forfait

                </a>



                <button type="button" onclick="document.getElementById('formPrestation').classList.toggle('hidden')"
                    class="px-4 py-2 bg-[#6B1F3A] text-white rounded-lg text-sm hover:bg-[#55172E]">

                    + Nouvelle prestation

                </button>

            </div>

        </div>

        {{-- FORMULAIRE AJOUT PRESTATION --}}

        <div id="formPrestation" class="hidden bg-white border border-[#E9E2DE] rounded-xl p-5 space-y-4">



            <div class="flex justify-between items-center">

                <h2 class="text-lg font-semibold">Nouvelle prestation</h2>



                <button type="button" onclick="document.getElementById('formPrestation').classList.add('hidden')"
                    class="text-[#827980] text-xl">

                    ×

                </button>

            </div>



            <form action="{{ route('prestations.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">

                @csrf

                <div>

                    <label class="block text-xs font-medium mb-2">Nom de la prestation</label>

                    <input type="text" name="nom" value="{{ old('nom') }}" required
                        placeholder="Exemple : Coupe homme"
                        class="w-full border border-[#E5DEDA] rounded-lg px-3 py-2 text-sm">

                </div>



                <div>

                    <label class="block text-xs font-medium mb-2">Catégorie</label>

                    <select name="categorie_service_id" required
                        class="w-full border border-[#E5DEDA] rounded-lg px-3 py-2 text-sm bg-white">

                        <option value="">Choisir une catégorie</option>

                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" @selected(old('categorie_service_id') == $cat->id)>

                                {{ $cat->nom }}

                            </option>
                        @endforeach

                    </select>

                </div>



                <div>

                    <label class="block text-xs font-medium mb-2">Prix (FCFA)</label>

                    <input type="number" name="prix" value="{{ old('prix') }}" min="0" required
                        placeholder="Exemple : 5000" class="w-full border border-[#E5DEDA] rounded-lg px-3 py-2 text-sm">

                </div>



                <div>

                    <label class="block text-xs font-medium mb-2">Durée (minutes)</label>

                    <input type="number" name="duree_minutes" value="{{ old('duree_minutes') }}" min="1" required
                        placeholder="Exemple : 30" class="w-full border border-[#E5DEDA] rounded-lg px-3 py-2 text-sm">

                </div>



                <div>

                    <label class="block text-xs font-medium mb-2">Zone</label>

                    <select name="zone" required
                        class="w-full border border-[#E5DEDA] rounded-lg px-3 py-2 text-sm bg-white">

                        <option value="mixte" @selected(old('zone', 'mixte') == 'mixte')>Mixte</option>

                        <option value="femmes" @selected(old('zone') == 'femmes')>Femmes</option>

                    </select>

                </div>



                <div class="md:col-span-2">

                    <label class="block text-xs font-medium mb-2">Description (facultative)</label>

                    <textarea name="description" rows="3" placeholder="Décrivez brièvement cette prestation..."
                        class="w-full border border-[#E5DEDA] rounded-lg px-3 py-2 text-sm">{{ old('description') }}</textarea>

                </div>



                <div class="md:col-span-2 flex justify-end gap-2">

                    <button type="button" onclick="document.getElementById('formPrestation').classList.add('hidden')"
                        class="px-4 py-2 border border-[#E9E2DE] rounded-lg text-xs">

                        Annuler

                    </button>



                    <button type="submit" class="px-4 py-2 bg-[#6B1F3A] text-white rounded-lg text-xs hover:bg-[#55172E]">

                        Enregistrer la prestation

                    </button>

                </div>



            </form>

        </div>



        {{-- ONGLETS --}}

        <div class="flex gap-7 border-b border-[#E9E2DE] text-sm">

            <a href="#prestations" class="pb-3 border-b-2 border-[#6B1F3A] text-[#6B1F3A] font-medium">

                Prestations

                <span class="ml-1 text-xs">{{ $total }}</span>

            </a>



            <a href="#forfaits" class="pb-3 text-[#827980] hover:text-[#6B1F3A]">

                Forfaits

                <span class="ml-1 text-xs">{{ $forfaits->count() }}</span>

            </a>



            <a href="#categories" class="pb-3 text-[#827980] hover:text-[#6B1F3A]">

                Catégories

                <span class="ml-1 text-xs">{{ $categories->count() }}</span>

            </a>

        </div>



        {{-- PRESTATIONS --}}

        <div id="prestations" class="grid grid-cols-1 lg:grid-cols-[180px_minmax(0,1fr)] gap-4">



            {{-- COLONNE GAUCHE --}}

            <aside class="space-y-3">



                <div class="bg-white border border-[#E9E2DE] rounded-xl p-2">



                    <a href="{{ route('prestations.index') }}"
                        class="flex justify-between items-center px-3 py-2 rounded-lg text-xs

                   {{ !request('categorie') ? 'bg-[#F5E9EE] text-[#6B1F3A] font-medium' : 'hover:bg-[#FAF7F5]' }}">

                        <span>◈ Toutes</span>

                        <span>{{ $total }}</span>

                    </a>



                    @foreach ($categories as $cat)
                        <a href="{{ route('prestations.index', array_merge(request()->except('categorie'), ['categorie' => $cat->id])) }}"
                            class="flex justify-between items-center px-3 py-2 rounded-lg text-xs

                       {{ request('categorie') == $cat->id ? 'bg-[#F5E9EE] text-[#6B1F3A] font-medium' : 'hover:bg-[#FAF7F5]' }}">



                            <span>{{ $cat->nom }}</span>

                            <span class="text-[#948B91]">{{ $cat->services_count }}</span>

                        </a>
                    @endforeach



                </div>



                {{-- ZONES --}}

                <div class="bg-white border border-[#E9E2DE] rounded-xl p-4 space-y-3">



                    <p class="text-[10px] font-semibold uppercase tracking-wider text-[#827980]">

                        Zones du salon

                    </p>



                    <div class="flex items-start gap-2 text-[11px] text-[#827980]">

                        <span class="px-2 py-1 rounded-full bg-[#E9EDF5] text-[#52678D] shrink-0">

                            Mixte

                        </span>

                        <span>Hommes, femmes et enfants.</span>

                    </div>



                    <div class="flex items-start gap-2 text-[11px] text-[#827980]">

                        <span class="px-2 py-1 rounded-full bg-[#F5E5ED] text-[#A3446D] shrink-0">

                            Femmes

                        </span>

                        <span>Réservée aux clientes. Les rendez-vous d'hommes y sont refusés.</span>

                    </div>



                </div>

            </aside>



            {{-- TABLEAU DES PRESTATIONS --}}

            <section class="bg-white border border-[#E9E2DE] rounded-xl overflow-hidden min-w-0">



                {{-- FILTRES --}}

                <form method="GET" action="{{ route('prestations.index') }}"
                    class="flex flex-col xl:flex-row gap-2 p-3 border-b border-[#EDE8E5]">



                    @if (request('categorie'))
                        <input type="hidden" name="categorie" value="{{ request('categorie') }}">
                    @endif



                    <input type="text" name="q" value="{{ request('q') }}"
                        placeholder="⌕  Rechercher une prestation..."
                        class="flex-1 min-w-0 border border-[#E5DEDA] rounded-lg px-3 py-2 text-xs outline-none focus:border-[#6B1F3A]">



                    <select name="zone" class="border border-[#E5DEDA] rounded-lg px-3 py-2 text-xs bg-white"
                        onchange="this.form.submit()">

                        <option value="">Toutes zones</option>

                        <option value="mixte" @selected(request('zone') == 'mixte')>Mixte</option>

                        <option value="femmes" @selected(request('zone') == 'femmes')>Femmes</option>

                    </select>



                    <select name="statut" class="border border-[#E5DEDA] rounded-lg px-3 py-2 text-xs bg-white"
                        onchange="this.form.submit()">

                        <option value="">Statut</option>

                        <option value="active" @selected(request('statut') == 'active')>Active</option>

                        <option value="inactive" @selected(request('statut') == 'inactive')>Inactive</option>

                    </select>



                    <button type="submit" class="px-4 py-2 rounded-lg text-xs bg-[#6B1F3A] text-white">

                        Rechercher

                    </button>

                </form>



                {{-- TABLEAU --}}

                <div class="overflow-x-auto">

                    <table class="w-full text-xs text-left whitespace-nowrap">



                        <thead class="bg-[#FAF8F6] text-[#827980] border-b border-[#E9E2DE]">

                            <tr>

                                <th class="px-3 py-3 font-medium">Prestation</th>

                                <th class="px-3 py-3 font-medium">Durée</th>

                                <th class="px-3 py-3 font-medium">Zone</th>

                                <th class="px-3 py-3 font-medium">Public</th>

                                <th class="px-3 py-3 font-medium">Réalisée par</th>

                                <th class="px-3 py-3 font-medium">Statut</th>

                                <th class="px-3 py-3 font-medium text-right">Prix</th>

                                <th class="px-3 py-3"></th>

                            </tr>

                        </thead>



                        <tbody>

                            @forelse($groupes as $nomCategorie => $items)



                                <tr class="bg-[#FAF8F6] border-b border-[#E9E2DE]">

                                    <td colspan="8"
                                        class="px-3 py-2 font-semibold text-[10px] uppercase tracking-wider text-[#827980]">

                                        {{ $nomCategorie }} · {{ $items->count() }}

                                    </td>

                                </tr>





                                @foreach ($items as $p)
                                    <tr class="border-b border-[#EDE8E5] hover:bg-[#FCFAF9]">



                                        {{-- PRESTATION --}}

                                        <td class="px-3 py-3">

                                            <div class="font-medium text-[#6B1F3A]">

                                                {{ $p->nom }}

                                            </div>



                                            @if (!$p->is_active)
                                                <div class="text-[10px] text-[#A39AA0] mt-1">

                                                    Désactivée le {{ $p->desactivee }}

                                                </div>
                                            @elseif($p->description)
                                                <div class="text-[10px] text-[#A39AA0] mt-1">

                                                    {{ $p->description }}

                                                </div>
                                            @endif

                                        </td>



                                        {{-- DURÉE --}}

                                        <td class="px-3 py-3">{{ $p->duree_format }}</td>



                                        {{-- ZONE --}}

                                        <td class="px-3 py-3">

                                            <span
                                                class="px-2 py-1 rounded-full text-[10px]

            {{ $p->zone === 'femmes' ? 'bg-[#F5E5ED] text-[#A3446D]' : 'bg-[#E9EDF5] text-[#52678D]' }}">

                                                {{ ucfirst($p->zone) }}

                                            </span>

                                        </td>



                                        {{-- PUBLIC --}}

                                        <td class="px-3 py-3">{{ $p->public }}</td>



                                        {{-- RÉALISATEURS --}}

                                        <td class="px-3 py-3">

                                            <div class="flex -space-x-1">

                                                @forelse($p->realisateurs as $initiales)
                                                    <span
                                                        class="w-6 h-6 flex items-center justify-center rounded-full bg-[#F0DFE5] text-[#6B1F3A] text-[9px] font-semibold border-2 border-white">

                                                        {{ $initiales }}

                                                    </span>

                                                @empty

                                                    <span class="text-[#A39AA0]">—</span>
                                                @endforelse

                                            </div>

                                        </td>



                                        {{-- STATUT --}}

                                        <td class="px-3 py-3">

                                            <span
                                                class="px-2 py-1 rounded-full text-[10px]

            {{ $p->is_active ? 'bg-[#E6F2EC] text-[#438365]' : 'bg-[#F0EEEE] text-[#8B8589]' }}">

                                                {{ $p->is_active ? 'Active' : 'Inactive' }}

                                            </span>

                                        </td>



                                        {{-- PRIX --}}

                                        <td class="px-3 py-3 text-right font-medium">

                                            {{ $p->prix_format }}

                                        </td>



                                        {{-- ACTIONS --}}

                                        {{-- ACTIONS --}}

                                        <td class="px-3 py-3 text-[#6B1F3A] text-center">

                                            <div class="flex items-center justify-center gap-3">



                                                {{-- MODIFIER --}}

                                                <button type="button"
                                                    onclick="document.getElementById('editPrestation-{{ $p->id }}').classList.toggle('hidden')"
                                                    class="hover:text-[#55172E]" title="Modifier">

                                                    ✎

                                                </button>



                                                {{-- ACTIVER / DÉSACTIVER --}}

                                                <form action="{{ route('prestations.statut', $p->id) }}" method="POST"
                                                    onsubmit="return confirm('Voulez-vous vraiment {{ $p->is_active ? 'désactiver' : 'activer' }} cette prestation ?')">

                                                    @csrf

                                                    @method('PATCH')



                                                    <button type="submit"
                                                        class="{{ $p->is_active ? 'text-red-600' : 'text-green-600' }}"
                                                        title="{{ $p->is_active ? 'Désactiver' : 'Activer' }}">

                                                        {{ $p->is_active ? '⊖' : '⊕' }}

                                                    </button>

                                                </form>



                                                {{-- SUPPRIMER --}}

                                                <form action="{{ route('prestations.destroy', $p->id) }}" method="POST"
                                                    onsubmit="return confirm('Voulez-vous vraiment supprimer définitivement cette prestation ?')">

                                                    @csrf

                                                    @method('DELETE')



                                                    <button type="submit" class="text-red-600 hover:text-red-800"
                                                        title="Supprimer">

                                                        🗑

                                                    </button>

                                                </form>



                                            </div>

                                        </td>

                                    </tr>



                                    {{-- FORMULAIRE DE MODIFICATION --}}

                                    <tr id="editPrestation-{{ $p->id }}" class="hidden bg-[#FAF8F6]">

                                        <td colspan="8" class="p-4">



                                            <form action="{{ route('prestations.update', $p->id) }}" method="POST"
                                                class="grid grid-cols-1 md:grid-cols-2 gap-4">



                                                @csrf

                                                @method('PUT')



                                                <div>

                                                    <label class="block text-xs font-medium mb-2">Nom de la
                                                        prestation</label>

                                                    <input type="text" name="nom" value="{{ $p->nom }}"
                                                        required
                                                        class="w-full border border-[#E5DEDA] rounded-lg px-3 py-2 text-sm">

                                                </div>



                                                <div>

                                                    <label class="block text-xs font-medium mb-2">Catégorie</label>

                                                    <select name="categorie_service_id" required
                                                        class="w-full border border-[#E5DEDA] rounded-lg px-3 py-2 text-sm bg-white">



                                                        @foreach ($categories as $cat)
                                                            <option value="{{ $cat->id }}"
                                                                @selected($p->categorie_service_id == $cat->id)>

                                                                {{ $cat->nom }}

                                                            </option>
                                                        @endforeach



                                                    </select>

                                                </div>



                                                <div>

                                                    <label class="block text-xs font-medium mb-2">Prix (FCFA)</label>

                                                    <input type="number" name="prix" value="{{ $p->prix }}"
                                                        min="0" required
                                                        class="w-full border border-[#E5DEDA] rounded-lg px-3 py-2 text-sm">

                                                </div>



                                                <div>

                                                    <label class="block text-xs font-medium mb-2">Durée (minutes)</label>

                                                    <input type="number" name="duree_minutes"
                                                        value="{{ $p->duree_minutes }}" min="1" required
                                                        class="w-full border border-[#E5DEDA] rounded-lg px-3 py-2 text-sm">

                                                </div>



                                                <div>

                                                    <label class="block text-xs font-medium mb-2">Zone</label>

                                                    <select name="zone" required
                                                        class="w-full border border-[#E5DEDA] rounded-lg px-3 py-2 text-sm bg-white">

                                                        <option value="mixte" @selected($p->zone == 'mixte')>Mixte</option>

                                                        <option value="femmes" @selected($p->zone == 'femmes')>Femmes</option>

                                                    </select>

                                                </div>



                                                <div class="md:col-span-2">

                                                    <label class="block text-xs font-medium mb-2">Description</label>

                                                    <textarea name="description" rows="3" class="w-full border border-[#E5DEDA] rounded-lg px-3 py-2 text-sm">{{ $p->description }}</textarea>

                                                </div>



                                                <div class="md:col-span-2 flex justify-end gap-2">

                                                    <button type="button"
                                                        onclick="document.getElementById('editPrestation-{{ $p->id }}').classList.add('hidden')"
                                                        class="px-4 py-2 border border-[#E9E2DE] rounded-lg text-xs">

                                                        Annuler

                                                    </button>



                                                    <button type="submit"
                                                        class="px-4 py-2 bg-[#6B1F3A] text-white rounded-lg text-xs">

                                                        Enregistrer les modifications

                                                    </button>

                                                </div>



                                            </form>

                                        </td>

                                    </tr>
                                @endforeach



                                @empty

                                    <tr>

                                        <td colspan="8" class="px-4 py-10 text-center text-[#A39AA0]">

                                            Aucune prestation trouvée.

                                        </td>

                                    </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>



                    {{-- PIED DU TABLEAU --}}

                    <div class="flex justify-between items-center px-4 py-4 border-t border-[#E9E2DE] text-xs text-[#827980]">

                        <span>{{ $affichees }} sur {{ $total }} prestations</span>



                        <a href="{{ request()->fullUrlWithQuery(['tout' => 1]) }}"
                            class="text-[#6B1F3A] font-medium hover:underline">

                            Afficher tout

                        </a>

                    </div>



                </section>

            </div>



            {{-- FORFAITS --}}

            <section id="forfaits" class="space-y-4">



                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">



                    <div>

                        <h2 class="text-lg font-semibold">Forfaits</h2>

                        <p class="text-xs text-[#827980] mt-1">

                            Pour les couples, les familles et les occasions.

                        </p>

                    </div>



                    <button type="button" onclick="document.getElementById('formForfait').classList.toggle('hidden')"
                        class="px-4 py-2 bg-[#6B1F3A] text-white rounded-lg text-sm hover:bg-[#55172E]">

                        + Nouveau forfait

                    </button>



                </div>





                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3">



                    {{-- FORMULAIRE NOUVEAU FORFAIT --}}

                    <div id="formForfait" class="hidden bg-white border border-[#E9E2DE] rounded-xl p-5 space-y-4">



                        <h3 class="text-sm font-semibold">Créer un nouveau forfait</h3>



                        <form action="{{ route('prestations.forfaits.store') }}" method="POST" class="space-y-4">

                            @csrf



                            <div>

                                <label class="block text-xs font-medium mb-1">Nom du forfait</label>

                                <input type="text" name="nom" required
                                    class="w-full border border-[#E9E2DE] rounded-lg p-3 text-sm">

                            </div>



                            <div>

                                <label class="block text-xs font-medium mb-1">Description</label>

                                <textarea name="description" rows="2" class="w-full border border-[#E9E2DE] rounded-lg p-3 text-sm"></textarea>

                            </div>



                            <div>

                                <label class="block text-xs font-medium mb-1">Prix du forfait (FCFA)</label>

                                <input type="number" name="prix" min="0" required
                                    class="w-full border border-[#E9E2DE] rounded-lg p-3 text-sm">

                            </div>



                            <div>

                                <label class="block text-xs font-medium mb-2">Prestations incluses</label>



                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">

                                    @foreach ($servicesForfaits as $service)
                                        @continue(!$service->is_active)
                                        <label class="flex items-center gap-2 text-xs border border-[#E9E2DE] rounded-lg p-3">
                                            <input type="checkbox" name="services[]" value="{{ $service->id }}">
                                            <span>
                                                {{ $service->nom }} — {{ number_format($service->prix, 0, ',', ' ') }} FCFA
                                            </span>
                                        </label>
                                    @endforeach

                                </div>

                            </div>



                            <div class="flex justify-end gap-2">

                                <button type="button"
                                    onclick="document.getElementById('formForfait').classList.add('hidden')"
                                    class="px-4 py-2 border border-[#E9E2DE] rounded-lg text-sm">

                                    Annuler

                                </button>



                                <button type="submit" class="px-4 py-2 bg-[#6B1F3A] text-white rounded-lg text-sm">

                                    Enregistrer le forfait

                                </button>

                            </div>



                        </form>

                    </div>





                    @forelse($forfaits as $f)



                        <div class="bg-white border border-[#E9E2DE] rounded-xl p-4">



                            <div class="flex justify-between items-start gap-2">

                                <h3 class="text-sm font-semibold">{{ $f->nom }}</h3>



                                <span
                                    class="px-2 py-1 rounded-full text-[10px]

            {{ $f->zone === 'femmes' ? 'bg-[#F5E5ED] text-[#A3446D]' : 'bg-[#E9EDF5] text-[#52678D]' }}">

                                    {{ ucfirst($f->zone) }}

                                </span>

                            </div>



                            <p class="text-[11px] text-[#827980] mt-1">

                                {{ $f->description }}

                            </p>



                            <ul class="my-4 space-y-2 text-xs text-[#554D52]">

                                @foreach ($f->lignes as $ligne)
                                    <li>· {{ $ligne }}</li>
                                @endforeach

                            </ul>



                            <div class="border-t border-[#EDE8E5] pt-3 flex justify-between items-end gap-2">

                                <div>

                                    @if ($f->reduction > 0)
                                        <div class="text-[10px] text-[#A39AA0] line-through">

                                            {{ number_format($f->prix_normal, 0, ',', ' ') }} FCFA

                                        </div>
                                    @endif



                                    <div class="text-lg font-semibold">

                                        {{ number_format($f->prix, 0, ',', ' ') }} FCFA

                                    </div>

                                </div>



                                @if ($f->reduction > 0)
                                    <span class="px-2 py-1 rounded-full bg-[#E6F2EC] text-[#438365] text-[10px]">

                                        −{{ $f->reduction }} %

                                    </span>
                                @endif

                            </div>



                            <p class="text-[10px] text-[#827980] mt-2">

                                Durée totale {{ $f->duree_format }}

                            </p>



                            <div class="border-t border-[#EDE8E5] mt-4 pt-3 flex flex-wrap gap-2">



                                <button type="button"
                                    onclick="document.getElementById('editForfait-{{ $f->id }}').classList.toggle('hidden')"
                                    class="px-3 py-2 border border-[#E9E2DE] rounded-lg text-xs text-[#6B1F3A]">

                                    Modifier

                                </button>



                                <form action="{{ route('prestations.forfaits.statut', $f->id) }}" method="POST">

                                    @csrf

                                    @method('PATCH')

                                    <button type="submit"
                                        class="px-3 py-2 border border-[#E9E2DE] rounded-lg text-xs text-[#554D52]">

                                        {{ $f->is_active ? 'Désactiver' : 'Activer' }}

                                    </button>

                                </form>



                                <form action="{{ route('prestations.forfaits.destroy', $f->id) }}" method="POST"
                                    onsubmit="return confirm('Voulez-vous vraiment supprimer ce forfait ?')">

                                    @csrf

                                    @method('DELETE')

                                    <button type="submit"
                                        class="px-3 py-2 border border-[#E9E2DE] rounded-lg text-xs text-red-600">

                                        Supprimer

                                    </button>

                                </form>



                            </div>



                            <div id="editForfait-{{ $f->id }}" class="hidden mt-4 border-t border-[#E9E2DE] pt-4">



                                <h3 class="text-sm font-semibold mb-3">Modifier le forfait</h3>



                                <form action="{{ route('prestations.forfaits.update', $f->id) }}" method="POST"
                                    class="space-y-3">



                                    @csrf

                                    @method('PUT')



                                    <div>

                                        <label class="block text-xs font-medium mb-1">Nom du forfait</label>

                                        <input type="text" name="nom" value="{{ $f->nom }}" required
                                            class="w-full border border-[#E9E2DE] rounded-lg p-3 text-sm">

                                    </div>



                                    <div>

                                        <label class="block text-xs font-medium mb-1">Description</label>

                                        <textarea name="description" rows="2" class="w-full border border-[#E9E2DE] rounded-lg p-3 text-sm">{{ $f->description }}</textarea>

                                    </div>



                                    <div>

                                        <label class="block text-xs font-medium mb-1">Prix du forfait (FCFA)</label>

                                        <input type="number" name="prix" min="0" value="{{ $f->prix }}"
                                            required class="w-full border border-[#E9E2DE] rounded-lg p-3 text-sm">

                                    </div>



                                    <div>

                                        <label class="block text-xs font-medium mb-2">Prestations incluses</label>



                                        <div class="grid grid-cols-1 gap-2">

                                            @foreach ($servicesForfaits as $service)
                                                @continue(!$service->is_active && !in_array($service->id, $f->service_ids))
                                                <label
                                                    class="flex items-center gap-2 text-xs border border-[#E9E2DE] rounded-lg p-3">
                                                    <input type="checkbox" name="services[]" value="{{ $service->id }}"
                                                        @checked(in_array($service->id, $f->service_ids))>
                                                    <span>
                                                        {{ $service->nom }} — {{ number_format($service->prix, 0, ',', ' ') }}
                                                        FCFA
                                                        @if (!$service->is_active)
                                                            <em class="text-[#A39AA0]">(désactivée)</em>
                                                        @endif
                                                    </span>
                                                </label>
                                            @endforeach

                                        </div>

                                    </div>



                                    <div class="flex justify-end gap-2">

                                        <button type="button"
                                            onclick="document.getElementById('editForfait-{{ $f->id }}').classList.add('hidden')"
                                            class="px-4 py-2 border border-[#E9E2DE] rounded-lg text-sm">

                                            Annuler

                                        </button>



                                        <button type="submit" class="px-4 py-2 bg-[#6B1F3A] text-white rounded-lg text-sm">

                                            Enregistrer les modifications

                                        </button>

                                    </div>



                                </form>

                            </div>



                        </div>



                    @empty

                        <div class="bg-white border border-[#E9E2DE] rounded-xl p-6 text-sm text-[#827980]">

                            Aucun forfait enregistré.

                        </div>

                    @endforelse





                </div>

            </section>



            {{-- GESTION DES CATÉGORIES --}}

            <section id="categories" class="space-y-4">



                <h2 class="text-lg font-semibold">Gestion des catégories</h2>



                <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">



                    {{-- LISTE DES CATÉGORIES --}}

                    <div class="bg-white border border-[#E9E2DE] rounded-xl overflow-hidden">



                        {{-- EN-TÊTE --}}

                        <div class="flex justify-between items-center p-4 border-b border-[#E9E2DE]">

                            <span class="font-semibold text-sm">Catégories</span>



                            <button type="button"
                                onclick="document.getElementById('formCategorie').classList.toggle('hidden')"
                                class="px-3 py-2 bg-[#6B1F3A] text-white rounded-lg text-xs hover:bg-[#55172E]">

                                + Ajouter

                            </button>

                        </div>



                        {{-- FORMULAIRE AJOUT --}}

                        <div id="formCategorie" class="hidden p-4 bg-[#FAF8F6] border-b border-[#E9E2DE]">



                            <form action="{{ route('prestations.categories.store') }}" method="POST" class="space-y-3">



                                @csrf



                                <div>

                                    <label class="block text-xs font-medium mb-2">

                                        Nom de la catégorie

                                    </label>



                                    <input type="text" name="nom" value="{{ old('nom') }}"
                                        placeholder="Exemple : Coiffure" required
                                        class="w-full border border-[#E5DEDA] rounded-lg px-3 py-2 text-xs outline-none focus:border-[#6B1F3A]">



                                    @error('nom')
                                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                                    @enderror

                                </div>



                                <div class="flex justify-end gap-2">

                                    <button type="button"
                                        onclick="document.getElementById('formCategorie').classList.add('hidden')"
                                        class="px-3 py-2 border border-[#E9E2DE] rounded-lg text-xs">

                                        Annuler

                                    </button>



                                    <button type="submit" class="px-4 py-2 bg-[#6B1F3A] text-white rounded-lg text-xs">

                                        Enregistrer

                                    </button>

                                </div>



                            </form>

                        </div>



                        {{-- AFFICHAGE DES CATÉGORIES --}}

                        @foreach ($categories as $cat)
                            <div
                                class="flex items-center justify-between px-4 py-4 border-b border-[#EDE8E5] text-xs hover:bg-[#FCFAF9]">



                                <div class="flex items-center gap-3">

                                    <span class="text-[#A39AA0]">⠿</span>

                                    <span class="font-semibold">{{ $cat->nom }}</span>

                                </div>



                                <div class="flex items-center gap-4">

                                    <span class="text-[#827980]">

                                        {{ $cat->services_count }} prestations

                                    </span>



                                    {{-- BOUTON MODIFIER --}}

                                    <button type="button"
                                        onclick="document.getElementById('editCategorie-{{ $cat->id }}').classList.toggle('hidden')"
                                        class="text-[#6B1F3A]">

                                        ✎

                                    </button>



                                    <button type="button"
                                        onclick="document.getElementById('deleteCategorie-{{ $cat->id }}').classList.toggle('hidden')"
                                        class="text-[#6B1F3A]">

                                        ⋯

                                    </button>

                                </div>



                            </div>



                            {{-- FORMULAIRE MODIFICATION --}}

                            <div id="editCategorie-{{ $cat->id }}"
                                class="hidden px-4 py-3 bg-[#FAF8F6] border-b border-[#E9E2DE]">



                                <form action="{{ route('prestations.categories.update', $cat->id) }}" method="POST"
                                    class="flex items-center gap-2">



                                    @csrf

                                    @method('PUT')



                                    <input type="text" name="nom" value="{{ $cat->nom }}" required
                                        class="flex-1 border border-[#E5DEDA] rounded-lg px-3 py-2 text-xs outline-none focus:border-[#6B1F3A]">



                                    <button type="submit" class="px-3 py-2 bg-[#6B1F3A] text-white rounded-lg text-xs">

                                        Enregistrer

                                    </button>



                                    <button type="button"
                                        onclick="document.getElementById('editCategorie-{{ $cat->id }}').classList.add('hidden')"
                                        class="px-3 py-2 border border-[#E9E2DE] rounded-lg text-xs">

                                        Annuler

                                    </button>



                                </form>

                            </div>



                            {{-- FORMULAIRE SUPPRESSION CATÉGORIE --}}

                            <div id="deleteCategorie-{{ $cat->id }}"
                                class="hidden px-4 py-3 bg-[#FAF8F6] border-b border-[#E9E2DE]">



                                <form action="{{ route('prestations.categories.destroy', $cat->id) }}" method="POST"
                                    class="flex items-center justify-between gap-3">



                                    @csrf

                                    @method('DELETE')



                                    <span class="text-xs text-[#827980]">

                                        Supprimer la catégorie « {{ $cat->nom }} » ?

                                    </span>



                                    <div class="flex gap-2">



                                        <button type="button"
                                            onclick="document.getElementById('deleteCategorie-{{ $cat->id }}').classList.add('hidden')"
                                            class="px-3 py-2 border border-[#E9E2DE] rounded-lg text-xs">

                                            Annuler

                                        </button>



                                        <button type="submit"
                                            onclick="return confirm('Voulez-vous vraiment supprimer cette catégorie ?')"
                                            class="px-3 py-2 bg-red-600 text-white rounded-lg text-xs">

                                            Supprimer

                                        </button>



                                    </div>



                                </form>

                            </div>
                        @endforeach



                        <p class="p-4 text-[10px] text-[#827980]">

                            Glisser pour réordonner (ordre d'affichage dans la prise de RDV).

                        </p>



                    </div>



                    {{-- PANNEAU D'INFORMATION --}}

                    <div class="bg-white border border-[#E9E2DE] rounded-xl p-5 h-fit">



                        <div class="flex items-start gap-3">

                            <span class="w-9 h-9 flex items-center justify-center rounded-full bg-[#F8EEDF] text-[#B17A36]">

                                !

                            </span>



                            <div>

                                <h3 class="text-sm font-semibold">

                                    Gestion des catégories

                                </h3>



                                <p class="text-xs text-[#827980] mt-2 leading-relaxed">

                                    Les catégories permettent de classer les prestations

                                    et de faciliter leur recherche lors de la prise de rendez-vous.

                                </p>

                            </div>

                        </div>



                    </div>



                </div>

            </section>

        </div>

    @endsection
