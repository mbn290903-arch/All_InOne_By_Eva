@extends('layouts.app')
@section('title', 'Dépenses')
@section('page-title', 'Dépenses')

@section('content')
    @include('partials.alertes')

    <div class="flex flex-wrap items-end justify-between gap-3">
        <form method="GET" class="flex flex-wrap items-end gap-2">
            <div>
                <label class="mb-1 block text-xs text-muted">Mois</label>
                <input type="month" name="mois" value="{{ $mois }}" class="rounded-lg border border-line px-3 py-2 text-sm">
            </div>
            <div>
                <label class="mb-1 block text-xs text-muted">Ou un jour précis</label>
                <input type="date" name="jour" value="{{ request('jour') }}" class="rounded-lg border border-line px-3 py-2 text-sm">
            </div>
            <div>
                <label class="mb-1 block text-xs text-muted">Catégorie</label>
                <select name="categorie" class="rounded-lg border border-line px-3 py-2 text-sm">
                    <option value="">Toutes</option>
                    @foreach ($categories as $c)
                        <option value="{{ $c->id }}" @selected(request('categorie') == $c->id)>{{ $c->nom }}</option>
                    @endforeach
                </select>
            </div>
            <button class="rounded-lg border border-line bg-white px-4 py-2 text-sm">Filtrer</button>
        </form>

        <a href="{{ route('depenses.create') }}" class="rounded-lg bg-wine px-4 py-2 text-sm text-white">+ Nouvelle dépense</a>
    </div>

    <p class="text-xs text-muted">Si un jour est choisi, il remplace le mois.</p>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl border border-line bg-white p-4 shadow-soft">
            <div class="text-xs text-muted">Dépenses d'aujourd'hui</div>
            <div class="mt-1 text-xl font-semibold">{{ number_format($depensesAujourdhui, 0, ',', ' ') }} FCFA</div>
        </div>
        <div class="rounded-xl border border-line bg-white p-4 shadow-soft">
            <div class="text-xs text-muted">Dépenses · {{ $libelle }}</div>
            <div class="mt-1 text-xl font-semibold">{{ number_format($totalDepenses, 0, ',', ' ') }} FCFA</div>
        </div>
        <div class="rounded-xl border border-line bg-white p-4 shadow-soft">
            <div class="text-xs text-muted">Recettes encaissées · {{ $libelle }}</div>
            <div class="mt-1 text-xl font-semibold">{{ number_format($recettes, 0, ',', ' ') }} FCFA</div>
        </div>
        <div class="rounded-xl border border-line bg-white p-4 shadow-soft">
            <div class="text-xs text-muted">Résultat estimé · {{ $libelle }}</div>
            <div class="mt-1 text-xl font-semibold {{ $resultat >= 0 ? 'text-green-700' : 'text-red-600' }}">
                {{ number_format($resultat, 0, ',', ' ') }} FCFA
            </div>
        </div>
    </div>

    <p class="text-xs text-muted">
        Résultat estimé = recettes encaissées − dépenses enregistrées sur la période (hors dépenses annulées).
        C'est un indicateur de gestion, pas un bénéfice comptable définitif.
    </p>

    @if ($parJour->count() > 1)
        <div class="rounded-xl border border-line bg-white p-4">
            <div class="mb-2 text-sm font-semibold">Dépenses par jour</div>
            <div class="flex flex-wrap gap-2">
                @foreach ($parJour as $jour => $total)
                    <a href="{{ route('depenses.index', ['jour' => $jour]) }}"
                        class="rounded-full bg-paper px-3 py-1 text-xs hover:bg-[#f6e9ed]">
                        {{ \Carbon\Carbon::parse($jour)->format('d/m') }} :
                        <strong>{{ number_format($total, 0, ',', ' ') }}</strong>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <div class="overflow-x-auto rounded-xl border border-line bg-white">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-line text-xs uppercase text-muted">
                <tr>
                    <th class="px-4 py-3">Date</th>
                    <th class="px-4 py-3">Catégorie</th>
                    <th class="px-4 py-3">Description</th>
                    <th class="px-4 py-3 text-right">Montant</th>
                    <th class="px-4 py-3">Justificatif</th>
                    <th class="px-4 py-3">Par</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($depenses as $d)
                    <tr class="border-b border-line last:border-0 {{ $d->estAnnulee() ? 'bg-red-50/40 text-muted' : '' }}">
                        <td class="px-4 py-3">{{ $d->date_depense->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">{{ $d->categorie->nom }}</td>
                        <td class="px-4 py-3">
                            <span class="{{ $d->estAnnulee() ? 'line-through' : '' }}">{{ $d->description }}</span>
                            @if ($d->estAnnulee())
                                <div class="mt-1 text-xs text-red-700">
                                    Annulée le {{ $d->annule_le->format('d/m/Y H:i') }}
                                    par {{ $d->annulePar?->name }} : {{ $d->motif_annulation }}
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right {{ $d->estAnnulee() ? 'line-through' : 'font-medium' }}">
                            {{ number_format($d->montant, 0, ',', ' ') }}
                        </td>
                        <td class="px-4 py-3">
                            @if ($d->justificatif_path)
                                <a href="{{ asset('storage/' . $d->justificatif_path) }}" target="_blank"
                                    class="text-xs text-wine hover:underline">Voir</a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">{{ $d->auteur->name }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-right">
                            @if ($d->estAnnulee())
                                <span class="rounded-full bg-red-100 px-2 py-0.5 text-xs text-red-700">Annulée</span>
                            @else
                                <button type="button" onclick="ouvrirAnnulation({{ $d->id }})"
                                    class="text-xs text-red-600 hover:underline">Annuler</button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-8 text-center text-muted">Aucune dépense sur cette période.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Modal d'annulation --}}
    <div id="modal-annuler" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 p-4">
        <form id="form-annuler" method="POST" class="w-full max-w-md space-y-3 rounded-xl bg-white p-5">
            @csrf
            @method('PATCH')
            <h3 class="text-base font-semibold">Annuler la dépense</h3>
            <p class="text-xs text-muted">
                La dépense restera visible, barrée, mais ne sera plus comptée dans les totaux.
            </p>
            <textarea name="motif_annulation" required minlength="5" maxlength="255" rows="3"
                placeholder="Motif obligatoire (ex : erreur de montant)"
                class="w-full rounded-lg border border-line px-3 py-2 text-sm"></textarea>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="fermerAnnulation()" class="rounded-lg border border-line px-4 py-2 text-sm">Retour</button>
                <button class="rounded-lg bg-red-600 px-4 py-2 text-sm text-white">Confirmer l'annulation</button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        const modalAnnuler = document.getElementById('modal-annuler');
        const formAnnuler = document.getElementById('form-annuler');

        function ouvrirAnnulation(id) {
            formAnnuler.action = "{{ url('depenses') }}/" + id + "/annuler";
            formAnnuler.querySelector('textarea').value = '';
            modalAnnuler.classList.remove('hidden');
            modalAnnuler.classList.add('flex');
        }

        function fermerAnnulation() {
            modalAnnuler.classList.add('hidden');
            modalAnnuler.classList.remove('flex');
        }
    </script>
@endpush