<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    // Test de la récupération de tous les produits
    public function test_can_get_all_products()
    {
        $response = $this->getJson('/api/products');

        $response->assertStatus(200);
    }

    // Test de la création d'un produit (authentifié)
    public function test_user_can_create_product()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $response = $this->postJson('/api/products', [
            'nom' => 'Test Product',
            'description' => 'Description du produit',
            'prix' => 100,
            'quantite' => 10,
        ]);

        $response->assertStatus(201)
                 ->assertJson(['data' => ['nom' => 'Test Product']]);
    }

    // Test de la mise à jour d'un produit (authentifié)
    public function test_user_can_update_product()
    {
        $product = Product::factory()->create();
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $response = $this->putJson("/api/products/{$product->id}", [
            'nom' => 'Updated Product',
            'prix' => 120,
        ]);

        $response->assertStatus(200)
                 ->assertJson(['data' => ['nom' => 'Updated Product']]);
    }

    // Test de la suppression d'un produit (authentifié)
    public function test_user_can_delete_product()
    {
        $product = Product::factory()->create();
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $response = $this->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}
