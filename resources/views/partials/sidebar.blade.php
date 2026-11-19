<aside id="sidebar" class="sidebar fixed inset-y-0 left-0 z-30 flex w-[255px] flex-col border-r border-line bg-white">

    {{-- Logo --}}
    <div class="flex h-[64px] items-center gap-3 border-b border-line px-5">
        <div class="grid h-[35px] w-[35px] place-items-center rounded-xl bg-wine text-white">
            <span class="font-serif text-lg italic">E</span>
        </div>

        <div>
            <div class="font-serif text-[18px] font-bold text-wine-dark">All in One</div>
            <div class="text-[12px] text-muted">by Eva · Beauté & bien-être</div>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto px-3 pb-4 pt-2">

        @php
            $user = auth()->user();
            $role = $user->role->name;

            $estAdmin = $user->isAdmin();
            $estReception = $role === 'receptionniste';
            $estPro = $role === 'professionnel';
            $adminOuReception = $estAdmin || $estReception;

            $peutGererClients = $estAdmin || $user->hasPermission('clients.gerer');
            $peutGererRdv = $estAdmin || $user->hasPermission('rdv.gerer');
            $peutEncaisser = $estAdmin || $user->hasPermission('paiements.enregistrer');
            $peutWhatsApp = $estAdmin || $user->hasPermission('whatsapp.envoyer');

            $routeDashboard =
                [
                    'receptionniste' => 'receptionniste.dashboard',
                    'professionnel' => 'professionnel.dashboard',
                    'admin' => 'admin.dashboard',
                ][$role] ?? 'admin.dashboard';

            $dashboardActif = request()->routeIs(
                'admin.dashboard',
                'receptionniste.dashboard',
                'professionnel.dashboard',
            );
        @endphp

        {{-- PILOTAGE --}}
        <div class="nav-label">PILOTAGE</div>

        <a href="{{ route($routeDashboard) }}" class="nav-link {{ $dashboardActif ? 'active' : '' }}">
            <i data-lucide="layout-dashboard"></i>
            Tableau de bord
        </a>

        @if ($estAdmin)
            <a href="{{ route('rapports.index') }}"
                class="nav-link {{ request()->routeIs('rapports.*') ? 'active' : '' }}">
                <i data-lucide="chart-no-axes-column-increasing"></i>
                Rapports
            </a>
        @endif

        {{-- ACTIVITÉ --}}
        <div class="nav-label">ACTIVITÉ</div>

        {{-- Rendez-vous / planning --}}
        @if ($estPro)
            <a href="{{ route('professionnel.planning') }}"
                class="nav-link {{ request()->routeIs('professionnel.planning', 'professionnel.rendez-vous.*') ? 'active' : '' }}">
                <i data-lucide="calendar"></i>
                Mon planning
            </a>
        @elseif ($peutGererRdv)
            <a href="{{ route('rendez-vous.index') }}"
                class="nav-link {{ request()->routeIs('rendez-vous.*') ? 'active' : '' }}">
                <i data-lucide="calendar"></i>
                Rendez-vous
            </a>
        @endif

        {{-- Clients --}}
        @if ($peutGererClients)
            <a href="{{ route('clients.index') }}"
                class="nav-link {{ request()->routeIs('clients.*') ? 'active' : '' }}">
                <i data-lucide="users"></i>
                Clients
            </a>
        @endif

        {{-- Catalogue en consultation --}}
        @if ($adminOuReception)
            <a href="{{ $estAdmin ? route('prestations.index') : route('catalogue.index') }}"
                class="nav-link {{ request()->routeIs('prestations.*', 'catalogue.*') ? 'active' : '' }}">
                <i data-lucide="sparkles"></i>
                Prestations
            </a>
        @endif

        {{-- Caisse --}}
        @if ($peutEncaisser)
            <a href="{{ route('caisse.index') }}"
                class="nav-link {{ request()->routeIs('caisse.*') ? 'active' : '' }}">
                <i data-lucide="wallet"></i>
                Caisse
            </a>
        @endif

        {{-- RELATION CLIENT --}}
        @if ($estAdmin || $peutWhatsApp)
            {{-- Campagnes : administratrice uniquement --}}
            @if ($estAdmin)
                <a href="{{ route('campagnes.index') }}"
                    class="nav-link {{ request()->routeIs('campagnes.*') ? 'active' : '' }}">
                    <i data-lucide="message-circle"></i>
                    WhatsApp & campagnes
                </a>
            @endif

            {{-- Anniversaires : administratrice uniquement --}}
            @if ($estAdmin)
                <a href="{{ route('anniversaires.index') }}"
                    class="nav-link {{ request()->routeIs('anniversaires.*') ? 'active' : '' }}">
                    <i data-lucide="cake"></i>
                    Anniversaires
                </a>
            @endif

            {{-- Fidélité --}}
            @if ($estAdmin)
                <a href="{{ route('fidelite.index') }}"
                    class="nav-link {{ request()->routeIs('fidelite.*') ? 'active' : '' }}">
                    <i data-lucide="gem"></i>
                    Fidélité
                </a>
            @endif
        @endif

        {{-- GESTION --}}
        @if ($estAdmin)

            <div class="nav-label">GESTION</div>

            {{-- Dépenses --}}
            <a href="{{ route('depenses.index') }}"
                class="nav-link {{ request()->routeIs('depenses.*') ? 'active' : '' }}">
                <i data-lucide="receipt"></i>
                Dépenses
            </a>

            {{-- Stocks --}}
            @if (Route::has('stock.index'))
                @php
                    $nbAlertes = class_exists(\App\Models\Produit::class)
                        ? \App\Models\Produit::stocksFaibles()->count()
                        : 0;
                @endphp

                <a href="{{ route('stock.index') }}"
                    class="nav-link {{ request()->routeIs('stock.*') ? 'active' : '' }}">
                    <i data-lucide="package"></i>
                    Stocks

                    @if ($nbAlertes > 0)
                        <span class="ml-auto rounded-full bg-[#fff2df] px-1.5 py-0.5 text-[9px] text-[#b77926]">
                            {{ $nbAlertes }}
                        </span>
                    @endif
                </a>
            @endif

            {{-- Employés --}}
            @can('viewAny', App\Models\Employe::class)
                <a href="{{ route('employes.index') }}"
                    class="nav-link {{ request()->routeIs('employes.*') ? 'active' : '' }}">
                    <i data-lucide="contact-round"></i>
                    Employés
                </a>
            @endcan

        @endif

        {{-- ADMINISTRATION --}}
        @if ($estAdmin)
            <div class="nav-label">ADMINISTRATION</div>

            <a href="{{ route('parametres.messages') }}"
                class="nav-link {{ request()->routeIs('parametres.*') ? 'active' : '' }}">
                <i data-lucide="settings"></i>
                Paramètres
            </a>
        @endif

    </nav>

    {{-- Profil utilisateur --}}
    <div class="border-t border-line px-4 py-4">

        <div class="mb-4 flex items-center gap-2">
            <div
                class="grid h-[34px] w-[34px] shrink-0 place-items-center rounded-full bg-[#f0dfe4] text-[10px] font-semibold text-wine">
                {{ strtoupper(substr($user->name, 0, 2)) }}
            </div>

            <div class="min-w-0 flex-1">
                <div class="truncate text-[11px] font-semibold">
                    {{ $user->name }}
                </div>

                <div class="text-[9px] text-muted">
                    {{ $role }}
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
