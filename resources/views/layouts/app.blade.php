<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'All in One By Eva')</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        wine: '#76243D',
                        'wine-dark': '#5D1830',
                        paper: '#F7F5F3',
                        line: '#EAE5E2',
                        muted: '#8B8588'
                    },
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui']
                    },
                    boxShadow: {
                        soft: '0 1px 3px rgba(40, 20, 25, .04)'
                    }
                }
            }
        }
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Inter, sans-serif;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 9px 12px;
            border-radius: 8px;
            color: #716b70;
            font-size: 12px;
            font-weight: 500;
        }

        .nav-link:hover {
            background: #f8f3f4;
            color: #76243d;
        }

        .nav-link.active {
            background: #f6e9ed;
            color: #76243d;
            font-weight: 600;
        }

        .nav-link svg {
            width: 15px;
            height: 15px;
            stroke-width: 1.7;
        }

        .nav-label {
            color: #aaa2a5;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: .12em;
            margin: 17px 12px 7px;
        }

        .panel {
            background: white;
            border: 1px solid #eae5e2;
            border-radius: 11px;
            box-shadow: 0 1px 3px rgba(40, 20, 25, .035);
        }

        .icon-tile {
            width: 29px;
            height: 29px;
            display: grid;
            place-items: center;
            border-radius: 8px;
            background: #f7ecef;
            color: #76243d;
            flex-shrink: 0;
        }

        .icon-tile svg {
            width: 15px;
            height: 15px;
        }

        .metric-label {
            color: #817a7e;
            font-size: 10px;
            line-height: 1.45;
        }

        .metric-value {
            color: #292327;
            font-size: 21px;
            line-height: 1.2;
            font-weight: 700;
            letter-spacing: -.04em;
        }

        .small-note {
            color: #8b8588;
            font-size: 9px;
            line-height: 1.5;
        }

        .section-label {
            color: #827a7f;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: .12em;
        }

        .chart-grid {
            stroke: #eee9e7;
            stroke-width: 1;
        }

        .chart-text {
            fill: #aaa2a5;
            font-size: 8px;
        }

        @media (max-width: 900px) {
            .sidebar {
                display: none;
            }

            .main-content {
                margin-left: 0 !important;
            }

            .desktop-search {
                display: none;
            }
        }
    </style>
</head>

<body class="bg-paper text-[#292327]">
    <div class="min-h-screen">

        {{-- Barre latérale --}}
        <aside class="sidebar fixed inset-y-0 left-0 z-30 flex w-[255px] flex-col border-r border-line bg-white">

            <div class="flex h-[64px] items-center gap-3 border-b border-line px-5">
                <div class="grid h-[35px] w-[35px] place-items-center rounded-xl bg-wine text-white">
                    <span class="font-serif text-lg italic">E</span>
                </div>
                <div>
                    <div class="font-serif text-[15px] text-wine-dark">All in One</div>
                    <div class="text-[9px] text-muted">by Eva · Beauté &amp; bien-être</div>
                </div>
            </div>

            <nav class="flex-1 overflow-y-auto px-3 pb-4 pt-2">

                <div class="nav-label">PILOTAGE</div>

                <a href="{{ route('dashboard') }}"
                    class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i data-lucide="layout-dashboard"></i>
                    Tableau de bord
                </a>

                <a href="#" class="nav-link">
                    <i data-lucide="chart-no-axes-column-increasing"></i>
                    Rapports
                </a>

                <div class="nav-label">ACTIVITÉ</div>

                <a href="#" class="nav-link">
                    <i data-lucide="calendar-days"></i>
                    Rendez-vous
                    <span class="ml-auto rounded-full bg-wine px-1.5 py-0.5 text-[9px] text-white">3</span>
                </a>

                <a href="#" class="nav-link">
                    <i data-lucide="users"></i>
                    Clients
                </a>

                {{-- LIEN VERS LA PAGE PRESTATIONS --}}
                <a href="{{ route('prestations.index') }}"
                    class="nav-link {{ request()->routeIs('prestations.*') ? 'active' : '' }}">
                    <i data-lucide="sparkles"></i>
                    Prestations
                </a>

                <a href="#" class="nav-link">
                    <i data-lucide="wallet"></i>
                    Caisse
                </a>

                <div class="nav-label">RELATION CLIENT</div>

                <a href="#" class="nav-link">
                    <i data-lucide="message-circle"></i>
                    WhatsApp &amp; campagnes
                </a>

                <a href="#" class="nav-link">
                    <i data-lucide="cake"></i>
                    Anniversaires
                    <span class="ml-auto rounded-full bg-[#fff2df] px-1.5 py-0.5 text-[9px] text-[#b77926]">4</span>
                </a>

                <a href="#" class="nav-link">
                    <i data-lucide="gem"></i>
                    Fidélité
                </a>

                <div class="nav-label">GESTION</div>

                <a href="#" class="nav-link">
                    <i data-lucide="receipt"></i>
                    Dépenses
                </a>

                <a href="#" class="nav-link">
                    <i data-lucide="package"></i>
                    Stocks
                    <span class="ml-auto rounded-full bg-[#fff2df] px-1.5 py-0.5 text-[9px] text-[#b77926]">2</span>
                </a>

                <a href="#" class="nav-link">
                    <i data-lucide="contact-round"></i>
                    Employés
                </a>

            </nav>

            <div class="border-t border-line p-3">
                <button type="button" class="nav-link w-full">
                    <i data-lucide="panel-left-close"></i>
                    Réduire le menu
                </button>
            </div>
        </aside>

        {{-- Contenu principal --}}
        <main class="main-content ml-[255px] min-h-screen">

            <header class="flex h-[64px] items-center justify-between border-b border-line bg-white px-6">
                <div>
                    <h1 class="text-sm font-semibold text-wine-dark">
                        @yield('header', 'Tableau de bord')
                    </h1>
                </div>

                <div class="flex items-center gap-4">
                    <span class="text-xs text-muted">
                        {{ now()->format('d/m/Y') }}
                    </span>

                    <button type="button" class="text-muted">
                        <i data-lucide="bell"></i>
                    </button>

                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-[#f6e9ed] text-sm font-semibold text-wine">
                        E
                    </div>
                </div>
            </header>

            <div class="p-6">
                @yield('content')
            </div>

        </main>
    </div>

    <script>
        lucide.createIcons();
    </script>

</body>

</html>