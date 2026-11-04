<header class="sticky top-0 z-20 flex h-[64px] items-center justify-between border-b border-line bg-white/95 px-7 backdrop-blur">
    <div class="text-[20px] font-medium">@yield('page-title', 'Tableau de bord')</div>

    <div class="flex items-center gap-3">
        <div class="desktop-search flex h-[34px] w-[320px] items-center gap-2 rounded-lg border border-[#ded8d6] px-3 text-[12px] text-[#aaa2a5]">
            <i data-lucide="search" class="h-3.5 w-3.5"></i>
            Rechercher un client, un téléphone,
            <span class="ml-auto rounded border px-1 py-0.5 text-[8px]">⌘K</span>
        </div>

        <div class="hidden items-center gap-2 text-[12px] text-[#817a7e] sm:flex">
            <i data-lucide="calendar" class="h-3.5 w-3.5"></i>
            {{ now()->translatedFormat('l j F Y') }}
        </div>

        <button class="relative grid h-[34px] w-[38px] place-items-center rounded-lg border border-[#ded8d6] text-[#716b70]">
            <i data-lucide="bell" class="h-4 w-4"></i>
            <span class="absolute right-2 top-1 h-1.5 w-1.5 rounded-full bg-red-500"></span>
        </button>
    </div>
</header>