@extends('layouts.app')
@section('title', 'Encaisser')
@section('page-title', 'Encaisser un paiement')

@section('content')
    @include('partials.alertes')

    <form method="POST" action="{{ route('caisse.store') }}"
        class="max-w-2xl space-y-4 rounded-xl border border-line bg-white p-5">
        @csrf

        <div>
            <label class="mb-1 block text-sm font-medium">Rendez-vous à encaisser</label>
            <select name="rendez_vous_id" id="rdv" class="w-full rounded-lg border border-line px-3 py-2 text-sm">
                <option value="">— Paiement sans rendez-vous —</option>
                @foreach ($rdvs as $r)
                    <option value="{{ $r->id }}" data-client="{{ $r->client_id }}"
                        data-total="{{ $r->prix_prevu }}" data-reste="{{ $r->prix_prevu - (int) $r->paye }}"
                        @selected(old('rendez_vous_id', $selectedId) == $r->id)>
                        {{ $r->debut->format('d/m/Y H:i') }} · {{ $r->client->prenom }} {{ $r->client->nom }}
                        · {{ $r->libellePrestations() }} · reste {{ number_format($r->prix_prevu - (int) $r->paye, 0, ',', ' ') }} FCFA
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="mb-1 block text-sm font-medium">Client</label>
            <select name="client_id" id="client" required class="w-full rounded-lg border border-line px-3 py-2 text-sm">
                <option value="">— Choisir —</option>
                @foreach ($clients as $c)
                    <option value="{{ $c->id }}" @selected(old('client_id') == $c->id)>
                        {{ $c->prenom }} {{ $c->nom }} ({{ $c->telephone }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="mb-1 block text-sm font-medium">Montant total (FCFA)</label>
                <input type="number" min="1" name="montant_total" id="total" value="{{ old('montant_total') }}"
                    class="w-full rounded-lg border border-line px-3 py-2 text-sm read-only:bg-paper">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">Montant payé (FCFA)</label>
                <input type="number" min="1" name="montant_paye" id="paye" value="{{ old('montant_paye') }}" required
                    class="w-full rounded-lg border border-line px-3 py-2 text-sm">
            </div>
        </div>

        <div class="text-sm">Reste à payer : <strong id="reste">—</strong></div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="mb-1 block text-sm font-medium">Mode de paiement</label>
                <select name="mode_paiement_id" required class="w-full rounded-lg border border-line px-3 py-2 text-sm">
                    @foreach ($modes as $m)
                        <option value="{{ $m->id }}" @selected(old('mode_paiement_id') == $m->id)>{{ $m->libelle }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">Référence (facultatif)</label>
                <input type="text" name="reference" value="{{ old('reference') }}"
                    class="w-full rounded-lg border border-line px-3 py-2 text-sm">
            </div>
        </div>

        <div class="flex justify-end gap-2">
            <a href="{{ route('caisse.index') }}" class="rounded-lg border border-line px-4 py-2 text-sm">Retour</a>
            <button id="btn-valider" class="rounded-lg bg-wine px-4 py-2 text-sm text-white">Enregistrer</button>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        const rdv = document.getElementById('rdv');
        const client = document.getElementById('client');
        const total = document.getElementById('total');
        const paye = document.getElementById('paye');
        const reste = document.getElementById('reste');

        function calculerReste() {
            const t = parseInt(total.value || 0);
            const p = parseInt(paye.value || 0);
            reste.textContent = t ? Math.max(0, t - p).toLocaleString('fr-FR') + ' FCFA' : '—';
        }

        function synchroniser() {
            const option = rdv.selectedOptions[0];
            if (rdv.value) {
                client.value = option.dataset.client;
                total.value = option.dataset.total;
                total.readOnly = true;
                if (!paye.value) paye.value = option.dataset.reste;
            } else {
                total.readOnly = false;
            }
            calculerReste();
        }

        rdv.addEventListener('change', () => { paye.value = ''; synchroniser(); });
        total.addEventListener('input', calculerReste);
        paye.addEventListener('input', calculerReste);

        // Empêche le double clic
        document.querySelector('form').addEventListener('submit', () => {
            document.getElementById('btn-valider').disabled = true;
        });

        synchroniser();
    </script>
@endpush