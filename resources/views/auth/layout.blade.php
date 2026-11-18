<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('titre') — All in One by Eva</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;1,500&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}?v=3">
<link rel="icon" type="image/png" href="{{ asset('favicon.png') }}?v=3">

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

    <aside class="relative hidden overflow-hidden bg-brand-800 text-cream lg:flex lg:w-[42%] lg:flex-col lg:justify-between lg:p-10">
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

        <div></div>
    </aside>

    <main class="flex flex-1 items-center justify-center bg-[#f9f7f4] px-6 py-12 sm:px-10">
        <div class="w-full max-w-sm">

            <div class="mb-8 flex items-center gap-3 lg:hidden">
                <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-brand-800 font-serif text-xl text-cream">E</span>
                <span class="font-serif text-2xl">All in One <em class="text-neutral-500">by Eva</em></span>
            </div>

            @yield('contenu')
        </div>
    </main>
</div>

@stack('scripts')
</body>
</html>