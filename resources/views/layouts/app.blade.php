<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'All in One by Eva')</title>

    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;1,500&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>

    @include('partials.styles')
    @stack('styles')
</head>

<body class="bg-paper text-[#292327]">

    <div class="min-h-screen">

        @include('partials.sidebar')

        <div id="sidebar-overlay" class="sidebar-overlay"></div>

        <main class="main-content ml-[255px] min-h-screen">

            @include('partials.navbar')

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
            sidebar?.classList.add('ouverte');
            overlay?.classList.add('visible');
        }

        function fermerMenu() {
            sidebar?.classList.remove('ouverte');
            overlay?.classList.remove('visible');
        }

        document.getElementById('menu-btn')?.addEventListener('click', ouvrirMenu);
        overlay?.addEventListener('click', fermerMenu);

        sidebar?.querySelectorAll('a').forEach(lien => {
            lien.addEventListener('click', fermerMenu);
        });

        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') fermerMenu();
        });
    </script>

    @stack('scripts')

</body>
</html>