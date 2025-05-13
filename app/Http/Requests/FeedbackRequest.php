<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FeedbackRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Tout le monde peut soumettre un feedback
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'rating' => 'required|integer|min:1|max:5',
            'emotion' => 'nullable|string|in:happy,neutral,sad,excited',
            'text' => 'required|string|min:10',
            'categories' => 'nullable|array',
            'categories.*' => 'string',
            'is_anonymous' => 'required|boolean',
        ];

        // Validation conditionnelle pour les champs non anonymes
        if ($this->is_anonymous == false) {
            $rules['name'] = 'nullable|string|max:255';
            $rules['email'] = 'nullable|email|max:255';
            $rules['want_response'] = 'boolean';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'rating.required' => 'Une évaluation est requise',
            'rating.integer' => 'L\'évaluation doit être un nombre entier',
            'rating.min' => 'L\'évaluation doit être au minimum de 1 étoile',
            'rating.max' => 'L\'évaluation doit être au maximum de 5 étoiles',
            'text.required' => 'Veuillez nous donner votre avis',
            'text.min' => 'Votre commentaire doit comporter au moins 10 caractères',
            'email.email' => 'Veuillez fournir une adresse email valide',
        ];
    }
}