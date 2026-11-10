<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnnulerPaiementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'motif_annulation' => ['required', 'string', 'min:5', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'motif_annulation.required' => "Le motif d'annulation est obligatoire.",
            'motif_annulation.min' => 'Le motif est trop court.',
        ];
    }
}