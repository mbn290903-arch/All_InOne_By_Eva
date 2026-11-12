<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDepenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // accès déjà limité à l'admin par le middleware
    }

    public function rules(): array
    {
        return [
            'categorie_depense_id' => ['required', 'exists:categories_depenses,id'],
            'montant' => ['required', 'integer', 'min:1'],
            'date_depense' => ['required', 'date', 'before_or_equal:today'],
            'description' => ['required', 'string', 'max:255'],
            'justificatif' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
        ];
    }

    public function messages(): array
    {
        return [
            'categorie_depense_id.required' => 'Choisissez une catégorie.',
            'montant.required' => 'Saisissez le montant.',
            'montant.integer' => 'Le montant doit être un nombre entier.',
            'montant.min' => 'Le montant doit être supérieur à 0.',
            'date_depense.required' => 'Choisissez la date.',
            'date_depense.before_or_equal' => 'La date ne peut pas être dans le futur.',
            'description.required' => 'Saisissez une description.',
            'justificatif.mimes' => 'Le justificatif doit être une image (jpg, png) ou un PDF.',
            'justificatif.max' => 'Le justificatif ne doit pas dépasser 4 Mo.',
        ];
    }
}