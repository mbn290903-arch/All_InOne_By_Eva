<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EncaisserPaiementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // l'accès est déjà contrôlé par le middleware de rôle
    }

    public function rules(): array
    {
        return [
            'client_id' => ['required', 'exists:clients,id'],
            'rendez_vous_id' => ['nullable', 'exists:rendez_vous,id'],
            'mode_paiement_id' => [
                'required',
                Rule::exists('modes_paiement', 'id')->where('is_active', true),
            ],
            'montant_paye' => ['required', 'integer', 'min:1'],
            'reference' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'client_id.required' => 'Choisissez un client.',
            'mode_paiement_id.required' => 'Choisissez un mode de paiement.',
            'montant_paye.required' => 'Saisissez le montant.',
            'montant_paye.integer' => 'Le montant doit être un nombre entier.',
            'montant_paye.min' => 'Le montant doit être supérieur à 0.',
        ];
    }
}