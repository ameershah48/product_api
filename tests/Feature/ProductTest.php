<?php

use App\Models\User;
use App\Models\Product;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it can list all products', function () {
    Sanctum::actingAs(User::factory()->create());
    Product::factory()->count(3)->create();

    $response = $this->getJson(route('products.index'));

    $response->assertStatus(200)
        ->assertJsonCount(3);
});

test('it can create a new product', function () {
    Sanctum::actingAs(User::factory()->create());

    $product = [
        'name' => 'New Product',
        'description' => 'A brand new product',
        'price' => 100,
    ];

    $response = $this->postJson(route('products.store'), $product);

    $response->assertStatus(201)
        ->assertJson([
            'name' => 'New Product',
            'description' => 'A brand new product',
            'price' => 100,
        ]);

    $this->assertDatabaseHas('products', $product);
});

test('it can update a product', function () {
    Sanctum::actingAs(User::factory()->create());

    $product = Product::factory()->create([
        'name' => 'Old Product',
        'description' => 'An old product',
        'price' => 100,
    ]);

    $update = [
        'name' => 'Updated Product',
        'description' => 'Updated product description',
        'price' => 200,
    ];

    $response = $this->putJson(route('products.update', ['product' => $product]), $update);

    $response->assertStatus(200)
        ->assertJson($update);

    $this->assertDatabaseHas('products', $update);
});

test('it can delete a product', function () {
    Sanctum::actingAs(User::factory()->create());

    $product = Product::factory()->create();

    $response = $this->deleteJson(route('products.destroy', ['product' => $product]));

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Product deleted successfully',
        ]);

    $this->assertDatabaseMissing('products', [
        'id' => $product->id,
    ]);
});
