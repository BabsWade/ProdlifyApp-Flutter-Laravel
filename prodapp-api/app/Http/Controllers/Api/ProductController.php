<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    // Liste des produits avec pagination et filtrage
    public function index(Request $request)
    {
        $products = Product::query();

        // Filtrage par nom, prix, catégorie
        if ($request->has('nom')) {
            $products->where('nom', 'like', '%' . $request->nom . '%');
        }
        if ($request->has('prix')) {
            $products->where('prix', '<=', $request->prix);
        }
        if ($request->has('categorie')) {
            $products->whereHas('categories', function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->categorie . '%');
            });
        }

        // Pagination
        $products = $products->paginate(10);

        return ProductResource::collection($products);
    }

    // Affichage d'un produit spécifique
    public function show($id)
    {
        $product = Product::findOrFail($id);

        return new ProductResource($product);
    }

    // Création d'un produit
    public function store(StoreProductRequest $request)
    {
        $product = Product::create($request->validated());

        if ($request->hasFile('image')) {
            $product->image = $request->file('image')->store('products', 's3');
            $product->save();
        }

        return new ProductResource($product);
    }

    // Mise à jour d'un produit
    public function update(UpdateProductRequest $request, $id)
    {
        $product = Product::findOrFail($id);
        $product->update($request->validated());

        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image (si elle existe)
            if ($product->image) {
                Storage::disk('s3')->delete($product->image);
            }
            $product->image = $request->file('image')->store('products', 's3');
            $product->save();
        }

        return new ProductResource($product);
    }

    // Suppression d'un produit
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        // Supprimer l'image du produit de S3
        if ($product->image) {
            Storage::disk('s3')->delete($product->image);
        }

        $product->delete();

        return response()->json(['message' => 'Produit supprimé avec succès.']);
    }
}
