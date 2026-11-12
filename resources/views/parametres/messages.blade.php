@extends('layouts.app')
@section('title', 'Modèles de messages — All in One by Eva')
@section('page-title', 'Paramètres · Modèles de messages')

@php
    // Variables utilisables, par type de modèle
    $variables = [
        'anniversaire' => ['[Prénom]', '[offre] (facultatif)'],
        'promotion'    => ['[Prénom]'],
        'rappel_rdv'   => ['[Prénom]', '[date]', '[heure]', '[prestation]'],
        'fidelisation' => ['[Prénom]'],
    ];
@endphp

@section('content')
<div class="mx-auto max-w-4xl space-y-5">

    <section class="rounded-[11px] border border-line bg-white p-5">
        <h2 class="text-[15px] font-semibold">Messages WhatsApp prédéfinis</h2>
        <p class="mt-1 text-sm text-muted">
            Ces textes préremplissent les messages WhatsApp. L'utilisateur peut toujours les relire et les modifier avant d'envoyer.
            Les variables entre crochets sont remplacées automatiquement. Aucun message n'est envoyé par l'application.
        </p>
    </section>

    @foreach ($modeles as $m)
        <form method="POST" action="{{ route('parametres.messages.update', $m) }}"
              class="rounded-[11px] border border-line bg-white p-5" data-modele>
            @csrf @method('PUT')

            <div class="flex flex-wrap items-baseline justify-between gap-2">
                <h3 class="font-semibold">{{ $m->titre }}</h3>
                <p class="text-xs text-muted">
                    Variables :
                     @foreach ($variables[$m->type instanceof \BackedEnum ? $m->type->value : $m->type] ?? ['[Prénom]'] as $v)
                        <code class="rounded bg-neutral-100 px-1.5 py-0.5">{{ $v }}</code>
                    @endforeach
                </p>
            </div>

            <textarea name="contenu" rows="4" required maxlength="1000" data-contenu
                      class="mt-3 w-full rounded-lg border px-3 py-2.5 text-sm focus:outline-none focus:ring-2 {{ session('status') === null && $errors->has('contenu') && old('_modele') == $m->id ? 'border-red-300 focus:ring-red-200' : 'border-neutral-200 focus:border-wine focus:ring-wine/20' }}">{{ old('_modele') == $m->id ? old('contenu') : $m->contenu }}</textarea>
            <input type="hidden" name="_modele" value="{{ $m->id }}">
            @if ($errors->has('contenu') && old('_modele') == $m->id)
                <p class="mt-1.5 text-xs text-red-600">{{ $errors->first('contenu') }}</p>
            @endif

            <div class="mt-3 rounded-lg bg-neutral-50 px-4 py-3">
                <p class="text-[11px] font-semibold uppercase tracking-wider text-muted">Aperçu</p>
                <p class="mt-1 whitespace-pre-line text-sm" data-apercu></p>
            </div>

            <div class="mt-4 flex flex-wrap justify-between gap-2">
                <button type="submit" name="reinitialiser" value="1" formnovalidate
                        onclick="return confirm('Rétablir le texte d\'origine de ce modèle ?')"
                        class="rounded-lg border border-neutral-300 px-4 py-2 text-sm font-medium hover:bg-neutral-50">Rétablir le texte d'origine</button>
                <button type="submit" class="rounded-lg bg-wine px-4 py-2 text-sm font-medium text-white hover:bg-wine-dark">Enregistrer</button>
            </div>
        </form>
    @endforeach
</div>

@if (session('status'))
    <div role="status" class="fixed bottom-6 left-6 z-50 max-w-sm rounded-xl bg-neutral-900 px-5 py-4 text-sm text-white shadow-lg">
        <strong class="block font-semibold">{{ session('status')[0] }}</strong>
        <span class="text-neutral-300">{{ session('status')[1] }}</span>
    </div>
@endif

@push('scripts')
<script>
    // Aperçu en direct avec des valeurs d'exemple (mêmes règles que le serveur : accents et majuscules ignorés)
    const exemples = { prenom: 'Mariama', nom: 'Ba', date: 'jeudi 12 novembre', heure: '10:30', prestation: 'Box braids', offre: 'un soin du visage offert' };
    const cle = s => s.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase().trim();
    const rendre = t => t.replace(/\[\s*([^\[\]]+?)\s*\]|\{\s*([^{}]+?)\s*\}/g, (m, a, b) => {
        const k = cle(a || b || '');
        return k in exemples ? exemples[k] : m;
    });
    document.querySelectorAll('[data-modele]').forEach(f => {
        const ta = f.querySelector('[data-contenu]'), ap = f.querySelector('[data-apercu]');
        const maj = () => { ap.textContent = rendre(ta.value); };
        ta.addEventListener('input', maj); maj();
    });
</script>
@endpush
@endsection
