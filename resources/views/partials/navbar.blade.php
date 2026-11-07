<header
    class="sticky top-0 z-20 flex h-[64px] items-center justify-between border-b border-line bg-white/95 px-4 backdrop-blur sm:px-7">
    <div class="flex min-w-0 items-center gap-3">
        <button type="button" id="menu-btn" aria-label="Ouvrir le menu"
            class="menu-btn h-[34px] w-[38px] shrink-0 place-items-center rounded-lg border border-[#ded8d6] text-[#716b70]">
            <i data-lucide="menu" class="h-4 w-4"></i>
        </button>

        <div class="truncate text-[18px] font-medium sm:text-[20px]">@yield('page-title', 'Tableau de bord')</div>
    </div>

    <div class="flex items-center gap-3">
        <label for="recherche-navbar"
            class="desktop-search flex h-[34px] w-[320px] items-center gap-2 rounded-lg border border-[#ded8d6] px-3 text-[12px] text-[#aaa2a5]">
            <i data-lucide="search" class="h-3.5 w-3.5"></i>
            <input type="search" id="recherche-navbar" autocomplete="off"
                placeholder="Rechercher un client, une prestation..."
                class="w-full bg-transparent text-[12px] text-gray-700 outline-none placeholder:text-[#aaa2a5]">
            <span class="ml-auto rounded border px-1 py-0.5 text-[8px]">⌘K</span>
        </label>

        <div class="hidden items-center gap-2 text-[12px] text-[#817a7e] sm:flex">
            <i data-lucide="calendar" class="h-3.5 w-3.5"></i>
            {{ now()->translatedFormat('l d F Y') }}
        </div>

        <button
            class="relative grid h-[34px] w-[38px] place-items-center rounded-lg border border-[#ded8d6] text-[#716b70]">
            <i data-lucide="bell" class="h-4 w-4"></i>
            <span class="absolute right-2 top-1 h-1.5 w-1.5 rounded-full bg-red-500"></span>
        </button>
    </div>
</header>
