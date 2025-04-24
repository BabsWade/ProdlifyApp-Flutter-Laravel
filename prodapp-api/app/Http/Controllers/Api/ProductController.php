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
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'prix' => 'required|numeric|min:0.01',
        'quantite' => 'required|integer|min:0',
        'image' => 'nullable|string',
        'categories' => 'sometimes|array',
        'categories.*' => 'exists:categories,id',
    ], [
        'name.required' => 'Le nom du produit est obligatoire.',
        'name.string' => 'Le nom doit être une chaîne de caractères.',
        'prix.required' => 'Le prix est obligatoire.',
        'prix.numeric' => 'Le prix doit être un nombre.',
        'prix.min' => 'Le prix doit être supérieur à 0.',
        'quantite.required' => 'La quantité est obligatoire.',
        'quantite.integer' => 'La quantité doit être un entier.',
        'image.url' => 'L\'URL de l\'image n\'est pas valide.',
        'categories.array' => 'Les catégories doivent être un tableau.',
        'categories.*.exists' => 'Une ou plusieurs catégories sont invalides.',
    ]);

    $product = Product::create([
        'name' => $validatedData['name'],
        'description' => $validatedData['description'] ?? null,
        'prix' => $validatedData['prix'],
        'quantite' => $validatedData['quantite'],
        'image' => $validatedData['image'] ?? null,
    ]);

    if ($request->has('categories')) {
        $product->categories()->sync($request->categories);
    }

    return response()->json([
        'message' => 'Produit créé avec succès!',
        'product' => $product->load('categories'),
    ], 201);
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

        $product->update($request->only(['name', 'description', 'prix', 'quantity', 'image']));

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
