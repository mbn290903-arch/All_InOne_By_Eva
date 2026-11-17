@extends('layouts.app')
@section('title', 'Impayés')
@section('page-title', 'Impayés')

@section('content')
    @include('caisse._onglets')
    @include('partials.alertes')

    <div class="rounded-xl border border-line bg-white p-4">
        <div class="text-xs text-muted">Total des impayés</div>
        <div class="text-xl font-semibold text-wine-dark">{{ number_format($totalImpaye, 0, ',', ' ') }} FCFA</div>
    </div>

    <div class="overflow-x-auto rounded-xl border border-line bg-white">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-line text-xs uppercase text-muted">
                <tr>
                    <th class="px-4 py-3">Rendez-vous</th>
                    <th class="px-4 py-3">Client</th>
                    <th class="px-4 py-3">Prestations</th>
                    <th class="px-4 py-3 text-right">Prix</th>
                    <th class="px-4 py-3 text-right">Payé</th>
                    <th class="px-4 py-3 text-right">Reste</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($impayes as $r)
                    <tr class="border-b border-line last:border-0">
                        <td class="px-4 py-3">{{ $r->debut->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3">{{ $r->client->prenom }} {{ $r->client->nom }}</td>
                        <td class="px-4 py-3">{{ $r->libellePrestations() }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format($r->prix_prevu, 0, ',', ' ') }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format((int) $r->paye, 0, ',', ' ') }}</td>
                        <td class="px-4 py-3 text-right font-semibold text-red-600">
                            {{ number_format($r->prix_prevu - (int) $r->paye, 0, ',', ' ') }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            @if (auth()->user()->aPermission('paiements.enregistrer'))
                                <a href="{{ route('caisse.create', ['rendez_vous_id' => $r->id]) }}"
                                    class="rounded-lg bg-wine px-3 py-1.5 text-xs text-white">Encaisser</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-muted">Aucun impayé.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
