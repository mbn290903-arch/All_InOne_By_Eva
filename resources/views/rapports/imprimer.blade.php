<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport du {{ $debut->format('d/m/Y') }} au {{ $fin->format('d/m/Y') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @include('partials.styles')
    <style>
        @page { size: A4; margin: 12mm; }
        @media print { .no-print { display: none !important; } body { background: #fff !important; } }
    </style>
</head>
<body class="bg-paper p-6 text-[#292327]">
    <div class="mx-auto max-w-[1000px] space-y-5">

        <div class="no-print flex items-center justify-between rounded-lg border border-line bg-white p-3 text-sm">
            <span>Pour obtenir un PDF : cliquez sur « Imprimer », puis choisissez « Enregistrer au format PDF ».</span>
            <div class="flex gap-2">
                <button onclick="window.print()" class="rounded-lg bg-wine px-4 py-2 text-white">Imprimer / PDF</button>
                <a href="{{ route('rapports.index', ['du' => $debut->toDateString(), 'au' => $fin->toDateString(), 'groupe' => $groupe]) }}"
                    class="rounded-lg border border-line px-4 py-2">Retour</a>
            </div>
        </div>

        <header>
            <div class="text-[22px] font-semibold text-wine-dark" style="font-family:'Cormorant Garamond', serif">All in One by Eva</div>
            <div class="text-[16px] font-semibold">Rapport d'activité</div>
            <div class="text-[12px] text-muted">
                Période : du {{ $debut->format('d/m/Y') }} au {{ $fin->format('d/m/Y') }} ·
                généré le {{ now()->format('d/m/Y à H:i') }} par {{ auth()->user()->name }}
            </div>
        </header>

        @include('rapports._contenu')
    </div>
</body>
</html>