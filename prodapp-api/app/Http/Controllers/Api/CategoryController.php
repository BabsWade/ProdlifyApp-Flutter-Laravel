<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;

class CategoryController extends Controller
{
    // Liste des catégories
    public function index()
    {
        $categories = Category::all();

        return CategoryResource::collection($categories);
    }

    // Affichage d'une catégorie spécifique
    public function show($id)
    {
        $category = Category::findOrFail($id);

        return new CategoryResource($category);
    }

    // Création d'une catégorie
    public function store(StoreCategoryRequest $request)
    {
        $category = Category::create($request->validated());

        return response()->json([
            'message' => 'Catégorie créée avec succès',
            'data' => $category
        ], 201);
    }

    // Mise à jour d'une catégorie
    public function update(UpdateCategoryRequest $request, $id)
    {
        $category = Category::findOrFail($id);
        $category->update($request->validated());

        return new CategoryResource($category);
    }

    // Suppression d'une catégorie
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json(['message' => 'Catégorie supprimée avec succès.']);
    }
}
