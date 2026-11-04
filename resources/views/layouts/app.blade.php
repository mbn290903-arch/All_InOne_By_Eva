<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'All in One')</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>

    {{-- Tes polices et tes styles CSS communs --}}
    @stack('styles')
</head>

<body class="bg-paper text-[#292327]">

    <div class="min-h-screen">

        {{-- Sidebar --}}
        @include('partials.sidebar')

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
    </script>

    @stack('scripts')

</body>
</html>