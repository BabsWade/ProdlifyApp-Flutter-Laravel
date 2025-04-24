<?php

namespace Tests\Unit;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductModelTest extends TestCase
{
    use RefreshDatabase;

    // Test de la validation du modèle Product
    public function test_product_validation()
    {
        $product = new Product([
            'nom' => 'Valid Product',
            'prix' => 50,
            'quantite' => 5
        ]);

        $this->assertTrue($product->validate());
    }

    // Test de la relation many-to-many avec les catégories
    public function test_product_has_categories()
    {
        $product = Product::factory()->create();
        $category = Category::factory()->create();

        $product->categories()->attach($category);

        $this->assertTrue($product->categories->contains($category));
    }
}
