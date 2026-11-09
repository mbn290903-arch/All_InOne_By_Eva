<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Connexion — All in One by Eva</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;1,500&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    {{-- Avec Vite : @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        serif: ['"Cormorant Garamond"', 'Georgia', 'serif'],
                    },
                    colors: {
                        brand: {
                            50: '#fbf3f5',
                            100: '#f7e4e8',
                            600: '#722640',
                            700: '#5f1f35',
                            800: '#4e1a2d',
                            900: '#3f1524',
                        },
                        cream: '#f7f3ee',
                    },
                },
            },
        }
    </script>
</head>

<body class="min-h-screen bg-[#f6f4f1] font-sans text-neutral-900 antialiased">
<div class="flex min-h-screen flex-col lg:flex-row">

    {{-- ===== Panneau gauche (branding) ===== --}}
    <aside class="relative hidden overflow-hidden bg-brand-800 text-cream lg:flex lg:w-[42%] lg:flex-col lg:justify-between lg:p-10">
        {{-- Cercles décoratifs --}}
        <svg class="pointer-events-none absolute -right-40 top-0 h-full w-[140%] opacity-20" viewBox="0 0 600 900" fill="none" aria-hidden="true">
            <circle cx="500" cy="300" r="260" stroke="#f7f3ee" stroke-width="1"/>
            <circle cx="500" cy="300" r="340" stroke="#f7f3ee" stroke-width="1"/>
            <circle cx="500" cy="300" r="420" stroke="#f7f3ee" stroke-width="1"/>
        </svg>

        <div class="relative flex items-center gap-3">
            <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-cream font-serif text-xl text-brand-800">E</span>
            <span class="text-[11px] font-semibold uppercase tracking-[0.15em] text-cream/70">Espace de gestion</span>
        </div>

        <div class="relative">
            <h1 class="font-serif text-6xl leading-none">All in One</h1>
            <p class="mt-4 font-serif text-3xl italic text-cream/80">by Eva</p>
            <p class="mt-8 max-w-xs text-sm leading-relaxed text-cream/80">
                Rendez-vous, clientes, caisse et fidélité : tout le salon au même endroit.
            </p>
        </div>

        <ul class="relative flex flex-wrap gap-x-6 gap-y-2 text-xs text-cream/60">
            <li class="flex items-center gap-2">
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="6" cy="6" r="3"/><circle cx="6" cy="18" r="3"/><path d="M20 4 8.1 15.9M14.5 14.5 20 20M8.1 8.1 12 12"/></svg>
                Coiffure
            </li>
            <li class="flex items-center gap-2">
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M12 3c3 4 5 6.5 5 10a5 5 0 0 1-10 0c0-3.5 2-6 5-10Z"/></svg>
                Soins &amp; massages
            </li>
            <li class="flex items-center gap-2">
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="m12 3 1.8 4.7L18.5 9.5l-4.7 1.8L12 16l-1.8-4.7L5.5 9.5l4.7-1.8L12 3Z"/></svg>
                Onglerie
            </li>
        </ul>
    </aside>

    {{-- ===== Panneau droit (formulaire) ===== --}}
    <main class="flex flex-1 items-center justify-center bg-[#f9f7f4] px-6 py-12 sm:px-10">
        <div class="w-full max-w-sm">

            {{-- Logo mobile --}}
            <div class="mb-8 flex items-center gap-3 lg:hidden">
                <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-brand-800 font-serif text-xl text-cream">E</span>
                <span class="font-serif text-2xl">All in One <em class="text-neutral-500">by Eva</em></span>
            </div>

            <h2 class="text-2xl font-semibold tracking-tight">Bon retour parmi nous</h2>
            <p class="mt-2 text-sm text-neutral-500">Connectez-vous pour accéder au planning du salon.</p>

            {{-- Message de statut (ex. mot de passe réinitialisé) --}}
            @if (session('status'))
                <div class="mt-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Erreur d'identifiants --}}
            @if ($errors->any())
                <div role="alert" class="mt-6 flex gap-3 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    <svg class="mt-0.5 h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 8v4m0 4h.01"/></svg>
                    <p>
                        <strong class="font-semibold">Identifiants incorrects.</strong>
                        {{ $errors->first('login') ?: $errors->first() }}
                    </p>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" id="login-form" class="mt-6 space-y-5" novalidate>
                @csrf

                {{-- Identifiant --}}
                <div>
                    <label for="email" class="mb-1.5 block text-[13px] font-medium">Adresse e-mail</label>
<div class="relative">
    <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-neutral-400" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
    <input
        id="email" name="email" type="email"
        value="{{ old('email') }}" required autofocus autocomplete="username"
        placeholder="nom@allinone-eva.sn"
        class="w-full rounded-lg border bg-white py-2.5 pl-10 pr-3 text-sm placeholder:text-neutral-400 focus:outline-none focus:ring-2
               {{ $errors->has('email') ? 'border-red-300 bg-red-50/40 focus:ring-red-200' : 'border-neutral-200 focus:border-brand-600 focus:ring-brand-600/20' }}">
</div>
                </div>

                {{-- Mot de passe --}}
                <div>
                    <div class="mb-1.5 flex items-center justify-between">
                        <label for="password" class="block text-[13px] font-medium">Mot de passe</label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-[13px] font-medium text-brand-600 hover:underline">Mot de passe oublié ?</a>
                        @endif
                    </div>
                    <div class="relative">
                        <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-neutral-400" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><rect x="5" y="11" width="14" height="9" rx="2"/><path d="M8 11V8a4 4 0 1 1 8 0v3"/></svg>
                        <input
                            id="password" name="password" type="password"
                            required autocomplete="current-password"
                            class="w-full rounded-lg border bg-white py-2.5 pl-10 pr-10 text-sm focus:outline-none focus:ring-2
                                   {{ $errors->has('login') || $errors->has('password') ? 'border-red-300 bg-red-50/40 focus:ring-red-200' : 'border-neutral-200 focus:border-brand-600 focus:ring-brand-600/20' }}">
                        <button type="button" id="toggle-password" aria-label="Afficher le mot de passe"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-neutral-400 hover:text-neutral-600">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>

                    @if (session('attempts_left') !== null)
                        <p class="mt-1.5 flex items-center gap-1.5 text-xs text-red-600">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 8v4m0 4h.01"/></svg>
                            Il reste {{ session('attempts_left') }} tentative{{ session('attempts_left') > 1 ? 's' : '' }}.
                        </p>
                    @endif
                </div>

                {{-- Rester connectée --}}
                <label class="flex cursor-pointer items-center gap-2 text-[13px]">
                    <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}
                           class="h-4 w-4 rounded border-neutral-300 text-brand-600 focus:ring-brand-600/30">
                    Rester connectée sur cet appareil
                </label>

                {{-- Bouton --}}
                <button type="submit" id="submit-btn"
                        class="flex w-full items-center justify-center gap-2 rounded-lg bg-brand-600 px-4 py-3 text-sm font-medium text-white transition hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-600/40 focus:ring-offset-2 disabled:cursor-not-allowed disabled:bg-brand-600/50">
                    <svg id="spinner" class="hidden h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="9" stroke="currentColor" stroke-width="3"/>
                        <path class="opacity-90" d="M21 12a9 9 0 0 0-9-9" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                    </svg>
                    <span id="submit-label">Se connecter</span>
                </button>
            </form>

            <p class="mt-8 text-center text-xs text-neutral-500">
                Problème d'accès ? Contactez l'administratrice du salon.
            </p>
        </div>
    </main>
</div>

<script>
    // Afficher / masquer le mot de passe
    const pwd = document.getElementById('password');
    document.getElementById('toggle-password').addEventListener('click', () => {
        pwd.type = pwd.type === 'password' ? 'text' : 'password';
    });

    // État de chargement du bouton à l'envoi
    document.getElementById('login-form').addEventListener('submit', () => {
        const btn = document.getElementById('submit-btn');
        btn.disabled = true;
        document.getElementById('spinner').classList.remove('hidden');
        document.getElementById('submit-label').textContent = 'Connexion en cours...';
    });
</script>
</body>
</html>