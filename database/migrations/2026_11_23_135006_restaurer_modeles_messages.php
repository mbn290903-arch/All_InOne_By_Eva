<?php

use App\Enums\TypeMessage;
use App\Models\ModeleMessage;
use App\Services\WhatsAppService;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        foreach (TypeMessage::cases() as $type) {
            $defaut = WhatsAppService::DEFAUTS[$type->value] ?? null;

            if ($defaut === null) {
                continue;
            }

            $existe = ModeleMessage::where('type', $type->value)->exists();

            if (! $existe) {
                ModeleMessage::create([
                    'type'    => $type,
                    'titre'   => method_exists($type, 'label') ? $type->label() : ucfirst(str_replace('_', ' ', $type->value)),
                    'contenu' => $defaut,
                ]);
            }
        }
    }

    public function down(): void
    {
        // Rien à annuler : on ne supprime pas de modèles.
    }
};