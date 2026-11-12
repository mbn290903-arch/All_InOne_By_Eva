<?php

namespace App\Http\Controllers;

use App\Models\ModeleMessage;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;

/** Paramètres › Modèles de messages WhatsApp (modifiables par l'administratrice). */
class ModeleMessageController extends Controller
{
    public function index()
    {
        $this->autoriser();

        return view('parametres.messages', [
            'modeles' => ModeleMessage::orderBy('id')->get(),
        ]);
    }

    public function update(Request $request, ModeleMessage $modele)
    {
        $this->autoriser();

        // Bouton « Rétablir le texte d'origine »
        if ($request->has('reinitialiser')) {
            $defaut = WhatsAppService::DEFAUTS[$modele->type->value] ?? null;
            abort_if($defaut === null, 404);

            $modele->contenu = $defaut;
            $modele->save();

            return back()->with('status', ['Modèle rétabli', "« {$modele->titre} » a retrouvé son texte d'origine."]);
        }

        $data = $request->validate(
            ['contenu' => 'required|string|min:10|max:1000'],
            [
                'contenu.required' => 'Le message ne peut pas être vide.',
                'contenu.min'      => 'Le message est trop court.',
                'contenu.max'      => 'Le message est trop long (1000 caractères maximum).',
            ]
        );

        $modele->contenu = trim($data['contenu']);
        $modele->save();

        return back()->with('status', ['Modèle enregistré', "« {$modele->titre} » a été mis à jour."]);
    }

    private function autoriser(): void
    {
        $u = auth()->user();
        abort_unless($u->isAdmin() || $u->hasPermission('parametres.gerer'), 403);
    }
}
