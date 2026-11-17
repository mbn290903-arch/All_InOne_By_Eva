@extends('auth.layout')
@section('titre', 'Mot de passe oublié')

@section('contenu')
    <h2 class="text-2xl font-semibold tracking-tight">Mot de passe oublié ?</h2>
    <p class="mt-2 text-sm text-neutral-500">
        Saisissez l'adresse e-mail de votre compte. Nous vous enverrons un lien pour choisir un nouveau mot de passe.
    </p>

    <div class="mt-6 space-y-3">
        @include('partials.alertes')
    </div>

    <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-5">
        @csrf

        <div>
            <label for="email" class="mb-1.5 block text-[13px] font-medium">Adresse e-mail</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                autocomplete="username" placeholder="nom@allinone-eva.sn"
                class="w-full rounded-lg border border-neutral-200 bg-white px-3 py-2.5 text-sm placeholder:text-neutral-400 focus:border-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-600/20">
        </div>

        <button type="submit"
            class="w-full rounded-lg bg-brand-600 px-4 py-3 text-sm font-medium text-white transition hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-600/40 focus:ring-offset-2">
            Envoyer le lien
        </button>
    </form>

    <a href="{{ route('login.show') }}" class="mt-8 block text-center text-[13px] font-medium text-brand-600 hover:underline">
        ← Retour à la connexion
    </a>
@endsection