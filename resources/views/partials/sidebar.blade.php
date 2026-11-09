<aside id="sidebar" class="sidebar fixed inset-y-0 left-0 z-30 flex w-[255px] flex-col border-r border-line bg-white">


    {{-- Logo --}}
    <div class="flex h-[64px] items-center gap-3 border-b border-line px-5">
        <div class="grid h-[35px] w-[35px] place-items-center rounded-xl bg-wine text-white">
            <span class="font-serif text-lg italic">E</span>
        </div>

        <div>
            <div class="font-serif text-[18px] font-bold text-wine-dark">All in One</div>
            <div class="text-[12px] text-muted">by Eva · Beauté &amp; bien-être</div>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto px-3 pb-4 pt-2">

        <div class="nav-label">PILOTAGE</div>

        @php
            $routeDashboard =
                [
                    'receptionniste' => 'receptionniste.dashboard',
                    'professionnel' => 'professionnel.dashboard',
                ][auth()->user()->role->name] ?? 'admin.dashboard';
        @endphp

        <a href="{{ route($routeDashboard) }}"
            class="nav-link {{ request()->routeIs($routeDashboard) ? 'active' : '' }}">
            <i data-lucide="layout-dashboard"></i>
            Tableau de bord
        </a>
        <a href="#" class="nav-link">
            <i data-lucide="chart-no-axes-column-increasing"></i>
            Rapports
        </a>

        <div class="nav-label">ACTIVITÉ</div>

        <a href="{{ route('rendez-vous.index') }}"
            class="nav-link {{ request()->routeIs('rendez-vous.*') ? 'active' : '' }}">
            <i data-lucide="calendar"></i>
            Rendez-vous
        </a>

        <a href="{{ route('clients.index') }}" class="nav-link {{ request()->routeIs('clients.*') ? 'active' : '' }}">
            <i data-lucide="users"></i>
            Clients
        </a>

        <a href="#" class="nav-link">
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

        <div class="nav-label">ADMINISTRATION</div>

        <a href="#" class="nav-link">
            <i data-lucide="settings"></i>
            Paramètres
        </a>

    </nav>

    {{-- Profil utilisateur --}}
    <div class="border-t border-line px-4 py-4">

        <div class="mb-4 flex items-center gap-2">
            <div
                class="grid h-[34px] w-[34px] shrink-0 place-items-center rounded-full bg-[#f0dfe4] text-[10px] font-semibold text-wine">
                {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
            </div>

            <div class="min-w-0 flex-1">
                <div class="truncate text-[11px] font-semibold">
                    {{ Auth::user()->name }}
                </div>

                <div class="text-[9px] text-muted">
                    {{ Auth::user()->role->name }}
                </div>
            </div>
        </div>

        {{-- Déconnexion --}}
        <form action="{{ route('logout') }}" method="POST">
            @csrf

            <button type="submit"
                class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-[11px] font-medium text-red-600 transition hover:bg-red-50">

                <i data-lucide="log-out" class="h-4 w-4"></i>
                Déconnexion
            </button>
        </form>

    </div>

</aside>
