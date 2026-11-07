<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'All in One by Eva')</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;1,500&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>

    {{-- Styles communs à toutes les pages (config Tailwind, police, sidebar) --}}
    @include('partials.styles')

    {{-- Styles propres à une page --}}
    @stack('styles')
</head>

<body class="bg-paper text-[#292327]">

    <div class="min-h-screen">

        {{-- Sidebar --}}
        @include('partials.sidebar')
        <div id="sidebar-overlay" class="sidebar-overlay"></div>

        {{-- Contenu principal --}}
        <main class="main-content ml-[255px] min-h-screen">

            {{-- Navbar --}}
            @include('partials.navbar')

            {{-- Contenu spécifique de chaque page --}}
            <div class="space-y-5 px-6 py-5">
                @yield('content')
            </div>

        </main>

    </div>

    <script>
        lucide.createIcons();

        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');

        function ouvrirMenu() {
            sidebar.classList.add('ouverte');
            overlay.classList.add('visible');
        }

        function fermerMenu() {
            sidebar.classList.remove('ouverte');
            overlay.classList.remove('visible');
        }

        document.getElementById('menu-btn').addEventListener('click', ouvrirMenu);
        overlay.addEventListener('click', fermerMenu);

        // Fermer le menu après un clic sur un lien
        sidebar.querySelectorAll('a').forEach(lien => lien.addEventListener('click', fermerMenu));

        // Touche Échap
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') fermerMenu();
        });
    <script>
        tailwind.config = { theme: { extend: {
            fontFamily: { sans: ['Inter','ui-sans-serif','system-ui','sans-serif'], serif: ['"Cormorant Garamond"','Georgia','serif'] },
            colors: { brand: { 50:'#fbf3f5', 100:'#f7e4e8', 600:'#722640', 700:'#5f1f35', 800:'#4e1a2d' } },
        } } }
    </script>
    <style>
        #sidebar { transition: width .2s ease; }
        #sidebar.collapsed { width: 4.5rem; }
        #sidebar.collapsed .label { display: none; }
        #sidebar.collapsed .brand-text { display: none; }
        #sidebar.collapsed .badge { display: none; }
        #sidebar.collapsed a, #sidebar.collapsed #collapse-btn { justify-content: center; }
        @media (prefers-reduced-motion: reduce) { #sidebar { transition: none; } }
    </style>
</head>
<body class="bg-[#f6f4f1] font-sans text-neutral-900 antialiased">
@php
    $user = auth()->user();
    $initials = collect(explode(' ', $user->name))->map(fn($w) => mb_strtoupper(mb_substr($w, 0, 1)))->take(2)->implode('');

    $icons = [
        'dashboard' => '<rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/>',
        'rapports'  => '<path d="M3 3v18h18"/><path d="M8 17v-5M13 17V8M18 17v-9"/>',
        'rdv'       => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4"/>',
        'clients'   => '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20c0-3.5 3-6 6.5-6s6.5 2.5 6.5 6"/><path d="M16 4.5a3.5 3.5 0 0 1 0 7M18 14.5c2 .7 3.5 2.6 3.5 5.5"/>',
        'prestations' => '<path d="m12 3 1.8 4.7L18.5 9.5l-4.7 1.8L12 16l-1.8-4.7L5.5 9.5l4.7-1.8L12 3Z"/><path d="M19 15v4M17 17h4"/>',
        'caisse'    => '<path d="M3 7h16a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7Z"/><path d="M3 7l12-3v3"/>',
        'whatsapp'  => '<path d="M21 12a8 8 0 0 1-11.6 7.1L4 20l1-4.6A8 8 0 1 1 21 12Z"/>',
        'anniv'     => '<path d="M4 20h16v-6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v6Z"/><path d="M12 12V8M12 4v.01M4 16c2 1.5 4 1.5 6 0s4-1.5 6 0 2 1.5 4 0"/>',
        'fidelite'  => '<path d="M6 4h12l3 5-9 11L3 9l3-5Z"/><path d="M3 9h18M9 4l3 16M15 4l-3 16"/>',
        'depenses'  => '<path d="M6 3h12v18l-3-2-3 2-3-2-3 2V3Z"/><path d="M9 8h6M9 12h6"/>',
        'stocks'    => '<path d="M12 3 4 7v10l8 4 8-4V7l-8-4Z"/><path d="m4 7 8 4 8-4M12 11v10"/>',
        'employes'  => '<rect x="3" y="5" width="18" height="14" rx="2"/><circle cx="9" cy="11" r="2"/><path d="M6 16c.5-1.5 1.7-2 3-2s2.5.5 3 2M15 10h3M15 13h3"/>',
        'settings'  => '<circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M2 12h3M19 12h3M4.9 4.9 7 7M17 17l2.1 2.1M4.9 19.1 7 17M17 7l2.1-2.1"/>',
    ];

    // [libellé, route (ou null si pas encore faite), icône, pastille, style pastille]
    // TODO : remplacer les pastilles par de vraies valeurs calculées en base
    $nav = [
        'Pilotage' => [
            ['Tableau de bord', ['receptionniste' => 'receptionniste.dashboard', 'professionnel' => 'professionnel.dashboard'][$user->role->name] ?? 'admin.dashboard', 'dashboard', null, null],
            ['Rapports', null, 'rapports', null, null],
        ],
        'Activité' => [
            ['Rendez-vous', null, 'rdv', 3, 'brand'],
            ['Clients', 'clients.index', 'clients', null, null],
            ['Prestations', null, 'prestations', null, null],
            ['Caisse', null, 'caisse', null, null],
        ],
        'Relation client' => [
            ['WhatsApp & campagnes', null, 'whatsapp', null, null],
            ['Anniversaires', null, 'anniv', 4, 'amber'],
            ['Fidélité', null, 'fidelite', null, null],
        ],
        'Gestion' => [
            ['Dépenses', null, 'depenses', null, null],
            ['Stocks', null, 'stocks', 2, 'amber'],
            ['Employés', null, 'employes', null, null],
        ],
    ];
    if ($user->role->name === 'admin') {
        $nav['Administration'] = [['Paramètres', null, 'settings', null, null]];
    }
    $svg = fn($k) => '<svg class="h-[18px] w-[18px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">'.$icons[$k].'</svg>';
@endphp

<div class="flex min-h-screen">
    {{-- ===== Sidebar ===== --}}
    <aside id="sidebar" class="sticky top-0 hidden h-screen w-64 shrink-0 flex-col border-r border-neutral-200 bg-white lg:flex">
        <div class="flex items-center gap-3 px-5 py-5">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-brand-800 font-serif text-xl text-white">E</span>
            <div class="brand-text leading-tight">
                <p class="font-serif text-lg">All in One</p>
                <p class="text-xs text-neutral-500">by Eva · Beauté &amp; bien-être</p>
            </div>
        </div>

        <nav class="flex-1 overflow-y-auto px-3 pb-4">
            @foreach ($nav as $group => $items)
                <p class="label mb-1 mt-5 px-3 text-[11px] font-semibold tracking-wider text-neutral-400">{{ mb_strtoupper($group) }}</p>
                @foreach ($items as [$label, $route, $icon, $badge, $style])
                    @php
                        $active = $route && request()->routeIs(explode('.', $route)[0] . (str_contains($route, '.') ? '.*' : ''));
                    @endphp
                    <a href="{{ $route ? route($route) : '#' }}" title="{{ $label }}"
                       class="relative mb-0.5 flex items-center gap-3 rounded-lg px-3 py-2 text-sm {{ $active ? 'bg-brand-100 font-medium text-brand-700' : 'text-neutral-600 hover:bg-neutral-100' }}">
                        @if ($active)<span class="absolute -left-3 top-1/2 h-5 w-[3px] -translate-y-1/2 rounded-r bg-brand-600"></span>@endif
                        {!! $svg($icon) !!}
                        <span class="label flex-1">{{ $label }}</span>
                        @if ($badge)
                            <span class="badge flex h-5 min-w-5 items-center justify-center rounded-full px-1.5 text-[11px] font-semibold {{ $style === 'brand' ? 'bg-brand-600 text-white' : 'bg-amber-100 text-amber-700' }}">{{ $badge }}</span>
                        @endif
                    </a>
                @endforeach
            @endforeach
        </nav>

        <div class="border-t border-neutral-200 p-3">
            <button type="button" id="collapse-btn" class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm text-neutral-600 hover:bg-neutral-100">
                <svg class="h-[18px] w-[18px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M9 4v16"/></svg>
                <span class="label">Réduire le menu</span>
            </button>
        </div>
    </aside>

    {{-- ===== Zone principale ===== --}}
    <div class="min-w-0 flex-1">
        <header class="flex items-center justify-between gap-4 border-b border-neutral-200 bg-white px-6 py-3 lg:px-8">
            <p class="shrink-0 text-sm text-neutral-500">@yield('breadcrumb')</p>

            <div class="flex items-center gap-3">
                               {{-- Recherche --}}
                <form method="GET" action="{{ route('clients.index') }}" role="search" class="hidden md:block">
                    <label class="relative block">
                        <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-neutral-400" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                        <input id="global-search" name="q" type="search"
                               value="{{ request()->routeIs('clients.index') ? request('q') : '' }}"
                               placeholder="Rechercher un client, un téléphone…"
                               class="w-72 rounded-lg border border-neutral-200 bg-white py-2 pl-9 pr-12 text-sm placeholder:text-neutral-400 focus:border-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-600/20">
                        <kbd class="pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 rounded border border-neutral-200 px-1.5 text-[11px] text-neutral-400">⌘K</kbd>
                    </label>
                </form>

                {{-- Date --}}
                <span class="hidden items-center gap-2 text-sm text-neutral-500 xl:flex">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4"/></svg>
                    {{ ucfirst(now()->translatedFormat('l j F Y')) }}
                </span>

                {{-- Notifications --}}
                <button type="button" aria-label="Notifications" class="relative rounded-lg border border-neutral-200 p-2 text-neutral-600 hover:bg-neutral-50">
                    <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M6 8a6 6 0 1 1 12 0c0 7 3 8 3 8H3s3-1 3-8Z"/><path d="M10 20a2 2 0 0 0 4 0"/></svg>
                    <span class="absolute right-1.5 top-1.5 h-2 w-2 rounded-full bg-red-500 ring-2 ring-white"></span>
                </button>

                {{-- Profil --}}
                <details class="relative">
                    <summary class="flex cursor-pointer list-none items-center gap-3 border-l border-neutral-200 pl-3">
                        <span class="flex h-9 w-9 items-center justify-center rounded-full bg-brand-100 text-xs font-semibold text-brand-700">{{ $initials }}</span>
                        <span class="hidden text-left leading-tight sm:block">
                            <span class="block text-sm font-medium">{{ $user->name }}</span>
                            <span class="block text-xs text-neutral-500">{{ $user->role->label ?? '' }}</span>
                        </span>
                        <svg class="h-4 w-4 text-neutral-400" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>
                    </summary>
                    <div class="absolute right-0 z-20 mt-2 w-48 rounded-lg border border-neutral-200 bg-white p-1 shadow-lg">
                        <form method="POST" action="{{ route('logout') }}">@csrf
                            <button class="w-full rounded-md px-3 py-2 text-left text-sm hover:bg-neutral-100">Se déconnecter</button>
                        </form>
                    </div>
                </details>
            </div>
        </header>

        <main class="px-6 py-8 lg:px-8">@yield('content')</main>
    </div>
</div>

<script>
    // Réduire / agrandir le menu (mémorisé)
    const sb = document.getElementById('sidebar');
    try { if (localStorage.getItem('sb-collapsed') === '1') sb.classList.add('collapsed'); } catch (e) {}
    document.getElementById('collapse-btn').addEventListener('click', () => {
        sb.classList.toggle('collapsed');
        try { localStorage.setItem('sb-collapsed', sb.classList.contains('collapsed') ? '1' : '0'); } catch (e) {}
    });
    // Raccourci ⌘K / Ctrl+K pour la recherche
    document.addEventListener('keydown', e => {
        if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k') {
            e.preventDefault();
            document.getElementById('global-search')?.focus();
        }
    });
</script>
</body>

</html>
