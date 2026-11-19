<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EncaisserPaiementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_id' => [
                'required',
                Rule::exists('clients', 'id')->whereNull('archived_at'),
            ],

            'rendez_vous_id' => [
                'nullable',
                Rule::exists('rendez_vous', 'id')->whereNotIn('statut', ['annule']),
            ],

            'mode_paiement_id' => [
                'required',
                Rule::exists('modes_paiement', 'id')->where('is_active', true),
            ],

            'montant_paye' => [
                'required',
                'integer',
                'min:1',
            ],

            'reference' => [
                'nullable',
                'string',
                'max:255',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'client_id.required' => 'Veuillez sélectionner un client.',
            'client_id.exists' => 'Ce client est introuvable ou archivé.',

            'rendez_vous_id.exists' => 'Ce rendez-vous est introuvable ou annulé.',

            'mode_paiement_id.required' => 'Veuillez sélectionner un mode de paiement.',
            'mode_paiement_id.exists' => 'Ce mode de paiement est indisponible.',

            'montant_paye.required' => 'Veuillez saisir le montant encaissé.',
            'montant_paye.integer' => 'Le montant doit être un nombre entier.',
            'montant_paye.min' => 'Le montant doit être supérieur à zéro.',

            'reference.max' => 'La référence ne doit pas dépasser 255 caractères.',
        ];
    }
}
