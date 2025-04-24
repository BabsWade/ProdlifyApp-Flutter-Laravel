<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('categories');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category_id')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }

        if ($request->filled('min_price')) {
            $query->where('prix', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('prix', '<=', $request->max_price);
        }

        $products = $query->paginate(10);
        return response()->json($products);
    }

    public function store(Request $request)
{
    $validatedData = $request->validate([
        'nom' => 'required|string|max:255',        // validation du nom
        'description' => 'nullable|string',        // validation de la description
        'prix' => 'required|numeric|min:0.01',     // validation du prix
        'quantite' => 'required|integer|min:0',    // validation de la quantité
        'image' => 'nullable|string',              // validation de l'image
    ]);

    // Création du produit avec les bonnes colonnes
    $product = Product::create([
        'nom' => $validatedData['nom'],
        'description' => $validatedData['description'],
        'prix' => $validatedData['prix'],
        'quantite' => $validatedData['quantite'],
        'image' => $validatedData['image'], // Assure-toi que l'image est validée et stockée
    ]);

    return response()->json(['message' => 'Produit créé avec succès!', 'product' => $product], 201);
}


    public function show($id)
    {
        $product = Product::with('categories')->findOrFail($id);
        return response()->json($product);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name'        => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'prix'       => 'sometimes|numeric|min:0.01',
            'quantity'    => 'sometimes|integer|min:0',
            'image'       => 'nullable|string',
            'categories'  => 'array',
            'categories.*' => 'exists:categories,id',
        ]);
        
        // Si la validation échoue
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $product->update($request->only(['nom', 'description', 'prix', 'quantity', 'image']));

        if ($request->has('categories')) {
            $product->categories()->sync($request->categories);
        }

        return response()->json(['message' => 'Produit mis à jour', 'data' => $product->load('categories')]);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->categories()->detach();
        $product->delete();

        return response()->json(['message' => 'Produit supprimé']);
    }
}
