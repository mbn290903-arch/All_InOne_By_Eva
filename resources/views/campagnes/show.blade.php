
@extends('layouts.app')

@section('title', $campagne->titre . ' — All in One by Eva')
@section('page-title', 'Suivi de campagne')

@section('content')
<div class="space-y-6">

    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <a href="{{ route('campagnes.index') }}"
                class="mb-2 inline-flex items-center gap-1 text-sm text-neutral-500 hover:text-neutral-800">
                <i data-lucide="arrow-left" class="h-4 w-4"></i>
                Retour aux campagnes
            </a>
            <h1 class="text-2xl font-semibold">{{ $campagne->titre }}</h1>
            <p class="mt-1 text-sm text-neutral-500">
                Créée le {{ $campagne->created_at->format('d/m/Y à H:i') }}
            </p>
        </div>

        <span class="rounded-full bg-amber-50 px-3 py-1.5 text-sm font-medium text-amber-800">
            Campagne préparée
        </span>
    </div>

    @if (session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid gap-4 sm:grid-cols-3">
        <div class="rounded-xl border border-neutral-200 bg-white p-5">
            <p class="text-sm text-neutral-500">Destinataires</p>
            <p class="mt-1 text-2xl font-semibold">
                {{ $campagne->destinataires()->count() }}
            </p>
        </div>

        <div class="rounded-xl border border-neutral-200 bg-white p-5">
            <p class="text-sm text-neutral-500">WhatsApp ouvert</p>
            <p class="mt-1 text-2xl font-semibold">
                {{ $campagne->destinataires()->whereNotNull('ouvert_le')->count() }}
            </p>
        </div>

        <div class="rounded-xl border border-neutral-200 bg-white p-5">
            <p class="text-sm text-neutral-500">Réponses reçues (suivi manuel)</p>
            <p class="mt-1 text-2xl font-semibold">
                {{ $campagne->destinataires()->where('suivi_manuel', 'réponse reçue')->count() }}
            </p>
        </div>
    </div>

    <section class="overflow-hidden rounded-xl border border-neutral-200 bg-white shadow-sm">
        <div class="border-b border-neutral-200 p-5">
            <h2 class="font-semibold">Suivi des destinataires</h2>
            <p class="mt-1 text-sm text-neutral-500">
                Ouvre WhatsApp pour préparer le message, puis actualise manuellement le statut.
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-neutral-50 text-xs uppercase text-neutral-500">
                    <tr>
                        <th class="px-4 py-3">Client</th>
                        <th class="px-4 py-3">Message</th>
                        <th class="px-4 py-3">Ouverture</th>
                        <th class="px-4 py-3">Suivi manuel</th>
                        <th class="px-4 py-3">Action</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-neutral-100">
                    @forelse ($destinataires as $destinataire)
                        <tr class="align-top">
                            <td class="px-4 py-4">
                                @if ($destinataire->client)
                                    <p class="font-medium">
                                        {{ $destinataire->client->prenom }}
                                        {{ $destinataire->client->nom }}
                                    </p>
                                    <p class="mt-1 text-xs text-neutral-500">
                                        {{ $destinataire->client->whatsapp ?: $destinataire->client->telephone }}
                                    </p>
                                @else
                                    <span class="text-red-600">Client indisponible</span>
                                @endif
                            </td>

                            <td class="max-w-sm px-4 py-4 text-neutral-600">
                                {{ $destinataire->message_personnalise }}
                            </td>

                            <td class="px-4 py-4">
                                @if ($destinataire->ouvert_le)
                                    <span class="text-green-700">
                                        {{ $destinataire->ouvert_le->format('d/m/Y H:i') }}
                                    </span>
                                @else
                                    <span class="text-neutral-400">Pas encore ouvert</span>
                                @endif
                            </td>

                            <td class="px-4 py-4">
                                <form method="POST"
                                    action="{{ route('campagnes.destinataires.suivi', $destinataire) }}">
                                    @csrf
                                    @method('PATCH')

                                    <select name="suivi_manuel"
                                        onchange="this.form.submit()"
                                        class="max-w-[190px] rounded-lg border border-neutral-300 px-2 py-2 text-xs">
                                        @foreach ([
                                            'à faire',
                                            'ouvert dans WhatsApp',
                                            'réponse reçue',
                                            'sans réponse',
                                            'à relancer',
                                            'ignoré'
                                        ] as $suivi)
                                            <option value="{{ $suivi }}"
                                                @selected($destinataire->suivi_manuel === $suivi)>
                                                {{ ucfirst($suivi) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
                            </td>

                            <td class="px-4 py-4">
                                @if ($destinataire->numero_normalise)
                                    <form method="POST"
                                        action="{{ route('campagnes.destinataires.ouvrir', $destinataire) }}"
                                        target="_blank">
                                        @csrf
                                        <button type="submit"
                                            class="inline-flex items-center gap-2 whitespace-nowrap rounded-lg bg-green-600 px-3 py-2 text-xs font-medium text-white hover:bg-green-700">
                                            <i data-lucide="message-circle" class="h-4 w-4"></i>
                                            Ouvrir WhatsApp
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-red-600">
                                        Numéro invalide
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-neutral-500">
                                Aucun destinataire dans cette campagne.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($destinataires->hasPages())
            <div class="border-t border-neutral-200 p-4">
                {{ $destinataires->links() }}
            </div>
        @endif
    </section>

    <p class="text-xs text-neutral-500">
        L'ouverture de WhatsApp est enregistrée par l'application.
        Cela ne prouve ni l'envoi ni la réception du message.
        Le statut de suivi reste modifiable manuellement.
    </p>

</div>
@endsection
