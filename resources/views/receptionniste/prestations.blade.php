@extends('layouts.app')

@section('title', 'Prestations & forfaits — All in One')
@section('page-title', 'Prestations')

@section('content')
<div class="space-y-7">
    {{-- En-tête --}}
    <section>
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-[20px] font-semibold tracking-tight">Prestations &amp; forfaits</h1>
                <p class="mt-1 text-[11px] text-muted">42 prestations · 6 forfaits · 7 catégories · <span class="rounded-full bg-[#fff1df] px-2 py-1 text-[9px] text-[#b87925]">Démo</span></p>
            </div>
            <div class="flex gap-2">
                <button type="button" class="flex items-center gap-2 rounded-lg border border-line bg-white px-3 py-2 text-[11px] font-medium hover:bg-[#faf8f7]" onclick="document.getElementById('forfaits').scrollIntoView({behavior:'smooth'})"><i data-lucide="package-plus" class="h-4 w-4"></i> Nouveau forfait</button>
                <button type="button" class="flex items-center gap-2 rounded-lg bg-wine px-3 py-2 text-[11px] font-medium text-white hover:bg-wine-dark" onclick="document.getElementById('form-prestation').classList.toggle('hidden')"><i data-lucide="plus" class="h-4 w-4"></i> Nouvelle prestation</button>
            </div>
        </div>
        <div class="mt-5 flex gap-6 border-b border-line text-[11px]">
            <a href="#liste-prestations" class="border-b-2 border-wine px-1 pb-2 font-semibold text-wine">Prestations <span class="ml-1 rounded-full bg-[#f6e9ed] px-1.5 py-0.5 text-[9px]">42</span></a>
            <a href="#forfaits" class="px-1 pb-2 text-[#716b70]">Forfaits <span class="ml-1 rounded-full bg-[#f1eeec] px-1.5 py-0.5 text-[9px]">6</span></a>
            <a href="#categories" class="px-1 pb-2 text-[#716b70]">Catégories <span class="ml-1 rounded-full bg-[#f1eeec] px-1.5 py-0.5 text-[9px]">7</span></a>
        </div>
    </section>

    {{-- Formulaire de démonstration --}}
    <section id="form-prestation" class="panel hidden p-4">
        <div class="mb-3 flex items-center justify-between"><h2 class="text-sm font-semibold">Nouvelle prestation</h2><button type="button" onclick="document.getElementById('form-prestation').classList.add('hidden')" class="text-muted">Fermer ✕</button></div>
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <label class="text-[10px] text-muted">Nom de la prestation<input class="mt-1 w-full rounded-lg border border-line p-2 text-xs" placeholder="Ex. Soin capillaire"></label>
            <label class="text-[10px] text-muted">Durée<input class="mt-1 w-full rounded-lg border border-line p-2 text-xs" placeholder="1 h 30"></label>
            <label class="text-[10px] text-muted">Zone<select class="mt-1 w-full rounded-lg border border-line p-2 text-xs"><option>Mixte</option><option>Femmes</option></select></label>
            <label class="text-[10px] text-muted">Prix (FCFA)<input type="number" class="mt-1 w-full rounded-lg border border-line p-2 text-xs" placeholder="15000"></label>
        </div>
        <p class="mt-3 text-[10px] text-muted">Maquette visuelle : le formulaire sera relié à la base de données dans une prochaine étape.</p>
    </section>

    {{-- Liste des prestations --}}
    <section id="liste-prestations" class="grid grid-cols-1 gap-3 xl:grid-cols-[180px_minmax(0,1fr)]">
        <div class="space-y-3">
            <div class="panel p-2">
                <div class="flex items-center justify-between rounded-lg bg-[#f6e9ed] px-3 py-2 text-[11px] font-semibold text-wine"><span><i data-lucide="layers" class="mr-2 inline h-3.5 w-3.5"></i>Toutes</span><span class="text-[9px]">42</span></div>
                @foreach ([['Coiffure',9,'scissors'],['Tresses',8,'wand-sparkles'],['Coloration & soins capillaires',6,'brush'],['Manucure & pédicure',7,'hand'],['Massages & corps',6,'flower-2'],['Soins du visage',4,'smile'],['Enfants',2,'baby']] as $cat)
                    <button type="button" class="flex w-full items-center justify-between gap-2 rounded-lg px-3 py-2 text-left text-[10px] text-[#514b50] hover:bg-[#faf6f7]"><span><i data-lucide="{{ $cat[2] }}" class="mr-2 inline h-3 w-3"></i>{{ $cat[0] }}</span><span class="text-[9px] text-muted">{{ $cat[1] }}</span></button>
                @endforeach
            </div>
            <div class="panel p-4">
                <h3 class="mb-3 text-[9px] font-bold tracking-wider text-muted">ZONES DU SALON</h3>
                <div class="mb-2 flex items-start gap-2 text-[9px]"><span class="rounded-full bg-[#e9eef6] px-2 py-1 text-[#536b8b]">◉ Mixte</span><span class="text-muted">Hommes, femmes et enfants.</span></div>
                <div class="flex items-start gap-2 text-[9px]"><span class="rounded-full bg-[#f6e5ed] px-2 py-1 text-[#a33d68]">♙ Femmes</span><span class="text-muted">Réservé aux clientes. Les rendez-vous d'hommes y sont refusés.</span></div>
            </div>
        </div>

        <div class="panel min-w-0 overflow-hidden">
            <div class="flex flex-wrap items-center gap-2 border-b border-line p-3">
                <input id="searchPrestation" oninput="filterPrestations()" class="min-w-[160px] flex-1 rounded-lg border border-line px-3 py-2 text-[10px]" placeholder="⌕  Rechercher une prestation...">
                <select class="rounded-lg border border-line px-2 py-2 text-[10px]"><option>Toutes zones</option><option>Mixte</option><option>Femmes</option></select>
                <div class="flex rounded-lg border border-line p-0.5 text-[9px]"><button class="rounded-md bg-[#f6f3f1] px-3 py-1.5">Toutes</button><button class="px-2 py-1.5 text-muted">Mixte</button><button class="px-2 py-1.5 text-muted">Femmes</button></div>
                <button class="rounded-full border border-line px-3 py-2 text-[10px]">＋ Statut</button><button class="rounded-full border border-line px-3 py-2 text-[10px]">＋ Durée</button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[700px] text-left text-[10px]">
                    <thead class="bg-[#faf8f7] text-[9px] text-[#817a7e]"><tr><th class="px-3 py-3 font-medium">Prestation</th><th class="px-3 py-3 font-medium">Durée</th><th class="px-3 py-3 font-medium">Zone</th><th class="px-3 py-3 font-medium">Public</th><th class="px-3 py-3 font-medium">Réalisée par</th><th class="px-3 py-3 font-medium">Statut</th><th class="px-3 py-3 font-medium">Prix</th><th></th></tr></thead>
                    <tbody id="prestationsRows">
                        @php
                        $groups = [
                          ['TRESSES · 8', [
                            ['Box braids · épaules','Mèches non fournies','2 h 30','Femmes','Femme','FD AM','35 000 FCFA','Active'],
                            ['Knotless braids · mi-dos','Prix « à partir de » selon longueur','3 h','Femmes','Femme','FD','dès 40 000 FCFA','Active'],
                            ['Tresses enfant','','1 h 30','Mixte','Enfant','FD','10 000 FCFA','Active']]],
                          ['COIFFURE · 9', [
                            ['Coupe homme + barbe','','45 min','Mixte','Homme','IK','5 000 FCFA','Active'],
                            ['Brushing + soin','','1 h','Mixte','Femme','AM','8 000 FCFA','Active'],
                            ['Défrisage complet','Désactivée le 01/09/2026','2 h','Mixte','Femme','—','15 000 FCFA','Inactive']]],
                          ['MASSAGES & CORPS · 6', [
                            ['Massage relaxant 60 min','Existe aussi en zone mixte (cabine 2)','1 h','Femmes','Femme','NS','20 000 FCFA','Active'],
                            ['Massage relaxant 60 min','','1 h','Mixte','Tous','NS','20 000 FCFA','Active']]],
                          ['MANUCURE & PÉDICURE · 7', [
                            ['Manucure semi-permanent','','1 h','Mixte','Tous','CG','12 000 FCFA','Active']]]
                        ];
                        @endphp
                        @foreach($groups as $group)
                            <tr class="bg-[#faf8f7]"><td colspan="8" class="border-y border-line px-3 py-2 text-[9px] font-bold tracking-wider text-[#817a7e]">{{ $group[0] }}</td></tr>
                            @foreach($group[1] as $item)
                            <tr class="prestation-row border-b border-line hover:bg-[#fdfbfb]">
                                <td class="px-3 py-3"><div class="font-semibold {{ $item[7] === 'Inactive' ? 'text-muted' : '' }}">{{ $item[0] }}</div>@if($item[1])<div class="mt-0.5 text-[9px] text-muted">{{ $item[1] }}</div>@endif</td>
                                <td class="px-3 py-3">{{ $item[2] }}</td><td class="px-3 py-3"><span class="rounded-full px-2 py-1 text-[9px] {{ $item[3] === 'Femmes' ? 'bg-[#f6e5ed] text-[#a33d68]' : 'bg-[#e9eef6] text-[#536b8b]' }}">{{ $item[3] }}</span></td>
                                <td class="px-3 py-3">{{ $item[4] }}</td><td class="px-3 py-3"><span class="rounded-full bg-[#eee8e4] px-1.5 py-1 text-[8px] font-semibold text-[#725b52]">{{ $item[5] }}</span></td>
                                <td class="px-3 py-3"><span class="rounded-full px-2 py-1 text-[9px] {{ $item[7] === 'Active' ? 'bg-[#e5f1eb] text-[#438268]' : 'bg-[#f1eeee] text-[#999]' }}">{{ $item[7] }}</span></td><td class="whitespace-nowrap px-3 py-3">{{ $item[6] }}</td><td class="px-3 py-3 text-wine">•••</td>
                            </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>