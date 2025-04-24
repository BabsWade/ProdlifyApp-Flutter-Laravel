<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Assurez-vous que l'utilisateur est autorisé à créer un produit
    }

    public function rules()
    {
        return [
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'prix' => 'required|numeric|min:0',
            'quantite' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validation de l'image
            'categorie_ids' => 'nullable|array',
            'categorie_ids.*' => 'exists:categories,id', // Valider les catégories associées
        ];
    }

    public function messages()
    {
        return [
            'nom.required' => 'Le nom du produit est requis.',
            'prix.required' => 'Le prix est requis et doit être un nombre.',
            'quantite.required' => 'La quantité est requise et doit être un entier.',
            'image.image' => 'Le fichier image doit être une image valide.',
        ];
    }
}
