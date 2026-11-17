@extends('auth.layout')
@section('titre', 'Nouveau mot de passe')

@section('contenu')
    <h2 class="text-2xl font-semibold tracking-tight">Nouveau mot de passe</h2>
    <p class="mt-2 text-sm text-neutral-500">8 caractères minimum, avec au moins une lettre et un chiffre.</p>

    <div class="mt-6 space-y-3">
        @include('partials.alertes')
    </div>

    <form method="POST" action="{{ route('password.update') }}" class="mt-6 space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div>
            <label for="email" class="mb-1.5 block text-[13px] font-medium">Adresse e-mail</label>
            <input id="email" name="email" type="email" value="{{ old('email', $email) }}" required
                autocomplete="username"
                class="w-full rounded-lg border border-neutral-200 bg-white px-3 py-2.5 text-sm focus:border-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-600/20">
        </div>

        <div>
            <label for="password" class="mb-1.5 block text-[13px] font-medium">Nouveau mot de passe</label>
            <input id="password" name="password" type="password" required autocomplete="new-password"
                class="w-full rounded-lg border border-neutral-200 bg-white px-3 py-2.5 text-sm focus:border-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-600/20">
        </div>

        <div>
            <label for="password_confirmation" class="mb-1.5 block text-[13px] font-medium">Confirmer le mot de passe</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required
                autocomplete="new-password"
                class="w-full rounded-lg border border-neutral-200 bg-white px-3 py-2.5 text-sm focus:border-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-600/20">
        </div>

        <label class="flex cursor-pointer items-center gap-2 text-[13px]">
            <input type="checkbox" id="afficher"
                class="h-4 w-4 rounded border-neutral-300 text-brand-600 focus:ring-brand-600/30">
            Afficher les mots de passe
        </label>

        <button type="submit"
            class="w-full rounded-lg bg-brand-600 px-4 py-3 text-sm font-medium text-white transition hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-600/40 focus:ring-offset-2">
            Enregistrer le nouveau mot de passe
        </button>
    </form>

    <a href="{{ route('login.show') }}" class="mt-8 block text-center text-[13px] font-medium text-brand-600 hover:underline">
        ← Retour à la connexion
    </a>
@endsection

@push('scripts')
    <script>
        document.getElementById('afficher').addEventListener('change', function () {
            const type = this.checked ? 'text' : 'password';
            document.getElementById('password').type = type;
            document.getElementById('password_confirmation').type = type;
        });
    </script>
@endpush