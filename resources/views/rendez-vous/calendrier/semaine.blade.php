@extends('layouts.app')

@section('title', 'Calendrier')
@section('page-title', 'Rendez-vous')

@section('content')

    @php
        $H = 56;
        $h0 = config('salon.grille_debut');
        $h1 = config('salon.grille_fin');
        $hauteurTotale = ($h1 - $h0) * $H;

        $vue = $vue ?? 'semaine';

        // Navigation selon la vue sélectionnée
        $precedente = match ($vue) {
            'jour' => $debut->copy()->subDay(),
            'mois' => $debut->copy()->subMonth(),
            default => $debut->copy()->subWeek(),
        };

        $suivante = match ($vue) {
            'jour' => $debut->copy()->addDay(),
            'mois' => $debut->copy()->addMonth(),
            default => $debut->copy()->addWeek(),
        };
    @endphp

    {{-- En-tête --}}
    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">

        <div class="flex items-center gap-2">

            <h1 class="text-2xl font-semibold">
                @if ($vue === 'jour')
                    {{ $debut->translatedFormat('l j F Y') }}
                @elseif ($vue === 'mois')
                    {{ $debut->translatedFormat('F Y') }}
                @else
                    {{ $debut->translatedFormat('j M') }} –
                    {{ $fin->translatedFormat('j M Y') }}
                @endif

            </h1>

            <a href="{{ route('rendez-vous.index', [
                'date' => $precedente->toDateString(),
                'vue' => $vue,
                'employe_id' => $employeId,
            ]) }}"
                class="border rounded px-2 py-1">‹</a>

            <a href="{{ route('rendez-vous.index', [
                'date' => now()->toDateString(),
                'vue' => $vue,
                'employe_id' => $employeId,
            ]) }}"
                class="border rounded px-3 py-1">Aujourd'hui</a>

            <a href="{{ route('rendez-vous.index', [
                'date' => $suivante->toDateString(),
                'vue' => $vue,
                'employe_id' => $employeId,
            ]) }}"
                class="border rounded px-2 py-1">›</a>

        </div>

        <a href="{{ route('rendez-vous.create') }}" class="bg-[#6b2340] text-white rounded px-4 py-2">
            + Nouveau rendez-vous
        </a>
    </div>

    {{-- Onglets --}}
    <div class="inline-flex border rounded mb-3 text-sm overflow-hidden">

        @foreach (['jour' => 'Jour', 'semaine' => 'Semaine', 'mois' => 'Mois', 'liste' => 'Liste'] as $cle => $libelle)
            <a href="{{ route('rendez-vous.index', [
                'date' => request('date', now()->toDateString()),
                'vue' => $cle,
                'employe_id' => $employeId,
            ]) }}"
                class="px-3 py-2 {{ $vue === $cle ? 'bg-rose-100 font-medium' : 'hover:bg-rose-50' }}">
                {{ $libelle }}
            </a>
        @endforeach
    </div>


    {{-- ===== La grille ===== --}}
    @if ($vue === 'mois')

        {{-- Calendrier mensuel --}}
        <div class="border rounded-xl bg-white overflow-hidden">

            {{-- En-têtes des jours --}}
            <div class="grid grid-cols-7 bg-gray-50 border-b">
                @foreach (['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'] as $jour)
                    <div class="p-3 text-center text-sm font-semibold text-gray-600 border-r">
                        {{ $jour }}
                    </div>
                @endforeach
            </div>

            {{-- Cases du calendrier --}}
            <div class="grid grid-cols-7">

                @foreach ($jours as $j)
                    <div class="min-h-[140px] border-r border-b p-2">

                        @if ($j !== null)
                            {{-- Numéro du jour --}}
                            <div class="mb-2">
                                <span
                                    class="inline-flex items-center justify-center w-7 h-7 rounded-full text-sm
                                {{ $j['date']->isToday() ? 'bg-rose-600 text-white' : 'text-gray-700' }}">
                                    {{ $j['date']->day }}
                                </span>
                            </div>

                            {{-- Rendez-vous du jour --}}
                            @foreach ($j['rdvs'] as $rdv)
                                <button type="button"
                                    class="rdv-bloc mb-1 block w-full cursor-pointer overflow-hidden rounded px-2 py-1 text-left text-xs {{ $rdv->statut->classes() }}"
                                    data-client="{{ $rdv->client->nom }}"
                                    data-date="{{ $rdv->debut->translatedFormat('l j F Y') }}"
                                    data-heure="{{ $rdv->debut->format('H:i') }} - {{ $rdv->fin->format('H:i') }}"
                                    data-prestations="{{ $rdv->libellePrestations() }}"
                                    data-employe="{{ $rdv->employe->nom_complet ?? 'Non attribué' }}"
                                    data-zone="{{ ucfirst(str_replace('_', ' ', $rdv->zone->value)) }}"
                                    data-prix="{{ number_format($rdv->prix_prevu, 0, ',', ' ') }} FCFA"
                                    data-statut="{{ $rdv->statut->label() }}"
                                    data-url="{{ route('rendez-vous.statut', $rdv) }}"
                                    data-actions="{{ json_encode($rdv->statut->actions()) }}"
                                    data-deplacable="{{ $rdv->statut->peutEtreDeplace() ? '1' : '0' }}"
                                    data-url-deplacer="{{ route('rendez-vous.deplacer', $rdv) }}"
                                    data-commentaire="{{ $rdv->commentaire }}">

                                    <div class="font-semibold truncate">
                                        {{ $rdv->debut->format('H:i') }}
                                        {{ $rdv->client->nom }}
                                    </div>

                                    <div class="truncate">
                                        {{ $rdv->libellePrestations() }}
                                    </div>

                                </button>
                            @endforeach
                        @endif

                    </div>
                @endforeach

            </div>
        </div>
    @elseif ($vue === 'liste')
        {{-- Liste des rendez-vous --}}
        <div class="overflow-x-auto border rounded-xl bg-white">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3">Heure</th>
                        <th class="px-4 py-3">Client</th>
                        <th class="px-4 py-3">Prestations</th>
                        <th class="px-4 py-3">Employé</th>
                        <th class="px-4 py-3">Statut</th>
                    </tr>
                </thead>

                <tbody class="divide-y">
                    @forelse ($rdvs as $rdv)
                        <tr class="rdv-bloc cursor-pointer hover:bg-gray-50" data-client="{{ $rdv->client->nom }}"
                            data-date="{{ $rdv->debut->translatedFormat('l j F Y') }}"
                            data-heure="{{ $rdv->debut->format('H:i') }} - {{ $rdv->fin->format('H:i') }}"
                            data-prestations="{{ $rdv->libellePrestations() }}"
                            data-employe="{{ $rdv->employe->nom_complet ?? 'Non attribué' }}"
                            data-zone="{{ ucfirst(str_replace('_', ' ', $rdv->zone->value)) }}"
                            data-prix="{{ number_format($rdv->prix_prevu, 0, ',', ' ') }} FCFA"
                            data-statut="{{ $rdv->statut->label() }}" data-url="{{ route('rendez-vous.statut', $rdv) }}"
                            data-actions="{{ json_encode($rdv->statut->actions()) }}"
                            data-deplacable="{{ $rdv->statut->peutEtreDeplace() ? '1' : '0' }}"
                            data-url-deplacer="{{ route('rendez-vous.deplacer', $rdv) }}"
                            data-commentaire="{{ $rdv->commentaire }}">
                            <td class="px-4 py-3">
                                {{ $rdv->debut->translatedFormat('d M Y') }}
                            </td>
                            <td class="px-4 py-3">
                                {{ $rdv->debut->format('H:i') }} - {{ $rdv->fin->format('H:i') }}
                            </td>
                            <td class="px-4 py-3">
                                {{ $rdv->client->nom }}
                            </td>
                            <td class="px-4 py-3">
                                {{ $rdv->libellePrestations() }}
                            </td>
                            <td class="px-4 py-3">
                                {{ $rdv->employe->nom_complet ?? 'Non attribué' }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="rounded px-2 py-1 text-xs {{ $rdv->statut->classes() }}">
                                    {{ $rdv->statut->label() }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                Aucun rendez-vous pour cette période.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @else
        {{-- Grille horaire Jour / Semaine --}}
        <div class="overflow-x-auto border rounded-xl bg-white">
            <div class="grid {{ $vue === 'jour' ? 'min-w-[600px]' : 'min-w-[900px]' }}"
                style="grid-template-columns: 56px repeat({{ $vue === 'jour' ? 1 : 7 }}, minmax(0, 1fr))">

                {{-- En-têtes des jours --}}
                <div class="border-b"></div>

                @foreach ($jours as $j)
                    <a href="{{ route('rendez-vous.index', [
                        'date' => $j['date']->toDateString(),
                        'vue' => 'jour',
                        'employe_id' => $employeId,
                    ]) }}"
                        class="border-b border-l px-3 py-2 hover:bg-rose-50 {{ $j['date']->isToday() ? 'bg-rose-50' : '' }}">

                        <div class="text-xs text-gray-500">
                            {{ $j['date']->translatedFormat('D') }}
                        </div>

                        <div class="flex justify-between items-baseline">
                            <span class="text-xl font-semibold">{{ $j['date']->day }}</span>
                            <span class="text-xs text-gray-500">
                                {{ $j['ferme'] ? 'Fermé' : $j['rdvs']->count() . ' RDV' }}
                            </span>
                        </div>
                    </a>
                @endforeach

                {{-- Heures --}}
                <div class="relative" style="height: {{ $hauteurTotale }}px">
                    @for ($h = $h0; $h < $h1; $h++)
                        <span class="absolute right-2 text-xs text-gray-400" style="top: {{ ($h - $h0) * $H }}px">
                            {{ sprintf('%02d:00', $h) }}
                        </span>
                    @endfor
                </div>

                {{-- Colonnes des jours --}}
                @foreach ($jours as $j)
                    <div class="relative border-l" style="height: {{ $hauteurTotale }}px">

                        @for ($h = 1; $h < $h1 - $h0; $h++)
                            <div class="absolute left-0 right-0 border-t border-gray-100"
                                style="top: {{ $h * $H }}px"></div>
                        @endfor

                        @if ($j['ferme'])
                            <div class="absolute inset-0 flex items-center justify-center text-sm text-gray-500"
                                style="background: repeating-linear-gradient(45deg,#f3f1ee,#f3f1ee 6px,#e9e6e2 6px,#e9e6e2 12px)">
                                Salon fermé
                            </div>
                        @else
                            @foreach ($j['rdvs'] as $rdv)
                                @php
                                    $minutes = ($rdv->debut->hour - $h0) * 60 + $rdv->debut->minute;
                                    $duree = $rdv->debut->diffInMinutes($rdv->fin);
                                    $top = ($minutes / 60) * $H;
                                    $hauteur = max(($duree / 60) * $H, 30);
                                    $largeur = 100 / $rdv->nb_colonnes;
                                    $gauche = $rdv->colonne * $largeur;
                                @endphp

                                <button type="button"
                                    title="{{ $rdv->client->nom }} : {{ $rdv->libellePrestations() }} ({{ $rdv->debut->format('H:i') }} - {{ $rdv->fin->format('H:i') }})"
                                    class="rdv-bloc absolute cursor-pointer overflow-hidden rounded border-l-4 px-1 text-left text-[11px] leading-tight {{ $rdv->statut->classes() }}"
                                    style="top: {{ $top }}px; height: {{ $hauteur }}px; left: calc({{ $gauche }}% + 2px); width: calc({{ $largeur }}% - 4px)"
                                    data-client="{{ $rdv->client->nom }}"
                                    data-date="{{ $rdv->debut->translatedFormat('l j F Y') }}"
                                    data-heure="{{ $rdv->debut->format('H:i') }} - {{ $rdv->fin->format('H:i') }}"
                                    data-prestations="{{ $rdv->libellePrestations() }}"
                                    data-employe="{{ $rdv->employe->nom_complet ?? 'Non attribué' }}"
                                    data-zone="{{ ucfirst(str_replace('_', ' ', $rdv->zone->value)) }}"
                                    data-prix="{{ number_format($rdv->prix_prevu, 0, ',', ' ') }} FCFA"
                                    data-statut="{{ $rdv->statut->label() }}"
                                    data-url="{{ route('rendez-vous.statut', $rdv) }}"
                                    data-actions="{{ json_encode($rdv->statut->actions()) }}"
                                    data-deplacable="{{ $rdv->statut->peutEtreDeplace() ? '1' : '0' }}"
                                    data-url-deplacer="{{ route('rendez-vous.deplacer', $rdv) }}"
                                    data-commentaire="{{ $rdv->commentaire }}">

                                    <div class="font-semibold truncate">
                                        {{ $rdv->client->nom }}
                                    </div>
                                    <div class="truncate">
                                        {{ $rdv->debut->format('H:i') }} - {{ $rdv->fin->format('H:i') }}
                                    </div>
                                    <div class="text-gray-500">
                                        {{ $rdv->libellePrestations() }}
                                    </div>
                                </button>
                            @endforeach

                            @if ($j['date']->isToday() && now()->hour >= $h0 && now()->hour < $h1)
                                <div class="pointer-events-none absolute left-0 right-0 border-t-2 border-red-600 z-10"
                                    style="top: {{ (((now()->hour - $h0) * 60 + now()->minute) / 60) * $H }}px">
                                </div>
                            @endif
                        @endif

                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ===== Modal détail du rendez-vous ===== --}}
    <div id="rdv-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 p-4">
        <div class="w-full max-w-md rounded-xl bg-white p-5 shadow-xl">

            <div class="mb-4 flex items-start justify-between">
                <div>
                    <h2 id="m-client" class="text-lg font-semibold"></h2>
                    <p id="m-date" class="text-sm text-gray-500"></p>
                </div>
                <button type="button" id="rdv-fermer"
                    class="cursor-pointer text-2xl leading-none text-gray-400 hover:text-gray-700">&times;</button>
            </div>

            <dl class="space-y-2 text-sm">
                <div class="flex justify-between gap-4">
                    <dt class="text-gray-500">Heure</dt>
                    <dd id="m-heure" class="font-medium"></dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-gray-500">Prestations</dt>
                    <dd id="m-prestations" class="text-right font-medium"></dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-gray-500">Employé</dt>
                    <dd id="m-employe" class="font-medium"></dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-gray-500">Zone</dt>
                    <dd id="m-zone" class="font-medium"></dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-gray-500">Prix prévu</dt>
                    <dd id="m-prix" class="font-medium"></dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-gray-500">Statut</dt>
                    <dd id="m-statut" class="font-medium"></dd>
                </div>
                <div id="m-commentaire-ligne" class="hidden">
                    <dt class="text-gray-500">Commentaire</dt>
                    <dd id="m-commentaire" class="mt-1 rounded bg-gray-50 p-2"></dd>
                </div>
            </dl>

            {{-- Actions de changement de statut --}}
            <div id="m-actions" class="mt-5 flex flex-wrap gap-2"></div>
            {{-- Déplacer le rendez-vous --}}
            <div id="m-deplacer" class="mt-4 hidden border-t pt-4">
                <form id="m-deplacer-form" method="POST" class="flex flex-wrap items-end gap-2">
                    @csrf
                    @method('PATCH')
                    <div>
                        <label for="m-nouveau-debut" class="mb-1 block text-xs text-gray-500">
                            Nouvelle date et heure
                        </label>
                        <input type="datetime-local" name="debut" id="m-nouveau-debut" required
                            class="rounded border px-2 py-1 text-sm">
                    </div>
                    <button type="submit" class="cursor-pointer rounded bg-[#6b2340] px-3 py-1.5 text-sm text-white">
                        Déplacer
                    </button>
                </form>
            </div>

            <p id="m-final" class="mt-4 hidden text-sm text-gray-500">
                Aucune action possible sur ce rendez-vous.
            </p>
            <form id="m-statut-form" method="POST" class="hidden">
                @csrf
                @method('PATCH')
                <input type="hidden" name="statut" id="m-statut-input">
            </form>

            <div class="mt-5 text-right">
                <button type="button" id="rdv-fermer-2"
                    class="cursor-pointer rounded border px-4 py-2 text-sm hover:bg-gray-50">Fermer</button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            const modal = document.getElementById('rdv-modal');

            function ouvrirModal(bloc) {
                const d = bloc.dataset;

                document.getElementById('m-client').textContent = d.client;
                document.getElementById('m-date').textContent = d.date;
                document.getElementById('m-heure').textContent = d.heure;
                document.getElementById('m-prestations').textContent = d.prestations;
                document.getElementById('m-employe').textContent = d.employe;
                document.getElementById('m-zone').textContent = d.zone;
                document.getElementById('m-prix').textContent = d.prix;
                document.getElementById('m-statut').textContent = d.statut;

                // Le commentaire n'apparaît que s'il existe
                document.getElementById('m-commentaire').textContent = d.commentaire;
                document.getElementById('m-commentaire-ligne').classList.toggle('hidden', !d.commentaire);
                // Boutons de changement de statut
                const conteneur = document.getElementById('m-actions');
                conteneur.innerHTML = '';

                JSON.parse(d.actions || '[]').forEach(a => {
                    const bouton = document.createElement('button');
                    bouton.type = 'button';
                    bouton.textContent = a.label;
                    bouton.className = 'cursor-pointer rounded border px-3 py-1.5 text-sm hover:bg-gray-50';

                    if (a.value === 'annule' || a.value === 'absent') {
                        bouton.classList.add('text-red-700', 'border-red-300');
                    }

                    bouton.addEventListener('click', () => {
                        if (a.value === 'annule' && !confirm('Annuler ce rendez-vous ?')) return;

                        const formulaire = document.getElementById('m-statut-form');
                        formulaire.action = d.url;
                        document.getElementById('m-statut-input').value = a.value;
                        formulaire.submit();
                    });

                    conteneur.appendChild(bouton);
                });
                // Déplacement
                const peutDeplacer = d.deplacable === '1';
                document.getElementById('m-deplacer').classList.toggle('hidden', !peutDeplacer);
                document.getElementById('m-deplacer-form').action = d.urlDeplacer;

                const champDate = document.getElementById('m-nouveau-debut');
                champDate.value = '';
                                @unless (auth()->user()->peutPlanifierDansLePasse())
                    const maintenant = new Date();
                    maintenant.setMinutes(maintenant.getMinutes() - maintenant.getTimezoneOffset());
                    champDate.min = maintenant.toISOString().slice(0, 16);
                @endunless

                // Message quand plus aucune action n'est possible
                const aucuneAction = JSON.parse(d.actions || '[]').length === 0 && !peutDeplacer;
                document.getElementById('m-final').classList.toggle('hidden', !aucuneAction);
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }

            function fermerModal() {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }

            // Clic sur un rendez-vous (jour, semaine, mois ou liste)
            document.querySelectorAll('.rdv-bloc').forEach(bloc => {
                bloc.addEventListener('click', () => ouvrirModal(bloc));
            });

            // Boutons de fermeture
            document.getElementById('rdv-fermer').addEventListener('click', fermerModal);
            document.getElementById('rdv-fermer-2').addEventListener('click', fermerModal);

            // Clic sur le fond sombre
            modal.addEventListener('click', e => {
                if (e.target === modal) fermerModal();
            });

            // Touche Échap
            document.addEventListener('keydown', e => {
                if (e.key === 'Escape') fermerModal();
            });

            // ===== Recherche (utilise la barre de recherche du navbar) =====
            const champRecherche = document.getElementById('recherche-navbar');

            // Minuscules + sans accents : "Séverine" trouve "severine"
            const normaliser = t => (t || '').toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');

            function filtrerRdv() {
                const terme = normaliser(champRecherche.value.trim());

                document.querySelectorAll('.rdv-bloc').forEach(bloc => {
                    const d = bloc.dataset;
                    const texte = normaliser(`${d.client} ${d.prestations} ${d.employe}`);
                    bloc.style.display = (terme === '' || texte.includes(terme)) ? '' : 'none';
                });
            }

            if (champRecherche) {
                champRecherche.addEventListener('input', filtrerRdv);
                filtrerRdv(); // si le champ garde une valeur au rechargement
            }
        </script>
    @endpush
@endsection
