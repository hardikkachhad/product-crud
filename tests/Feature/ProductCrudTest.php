<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Product;

class ProductCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_can_be_created(): void
    {
        $response = $this->postJson('/api/products', [
            'name' => 'Laptop',
            'price' => 45000,
            'quantity' => 5,
        ]);

        $response->assertCreated()
            ->assertJsonPath('name', 'Laptop');

        $this->assertDatabaseHas('products', [
            'name' => 'Laptop',
            'quantity' => 5,
        ]);
    }

    public function test_products_can_be_listed(): void
    {
        Product::create([
            'name' => 'Laptop',
            'price' => 45000,
            'quantity' => 5,
        ]);

        $response = $this->getJson('/api/products');

        $response->assertOk()
            ->assertJsonFragment([
                'name' => 'Laptop',
            ]);
    }

    public function test_product_can_be_updated(): void
    {
        $product = Product::create([
            'name' => 'Laptop',
            'price' => 45000,
            'quantity' => 5,
        ]);

        $response = $this->putJson(
            "/api/products/{$product->id}",
            [
                'name' => 'HP Laptop',
                'price' => 48000,
                'quantity' => 3,
            ]
        );

        $response->assertOk()
            ->assertJsonPath('name', 'HP Laptop');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'HP Laptop',
        ]);
    }

    public function test_product_can_be_deleted(): void
    {
        $product = Product::create([
            'name' => 'Laptop',
            'price' => 45000,
            'quantity' => 5,
        ]);

        $response = $this->deleteJson(
            "/api/products/{$product->id}"
        );

        $response->assertOk();

        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    }
}
