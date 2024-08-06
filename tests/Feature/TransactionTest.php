<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Transaction;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticate()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);
        return ['Authorization' => "Bearer $token"];
    }

    public function test_user_can_record_purchase()
    {
        $headers = $this->authenticate();

        $product = Product::factory()->create([
            'name' => 'Test Product',
            'price' => 50.00,
            'quantity' => 100,
        ]);

        $response = $this->postJson('/api/purchase', [
            'product_id' => $product->id,
            'quantity' => 50,
            'price' => 45.00,
            'date' => '2024-08-01'
        ], $headers);

        $response->assertStatus(201)
                 ->assertJsonStructure(['message', 'transaction']);
    }

    public function test_user_can_record_sale()
    {
        $headers = $this->authenticate();

        $product = Product::factory()->create([
            'name' => 'Test Product',
            'price' => 50.00,
            'quantity' => 100,
        ]);

        $response = $this->postJson('/api/sale', [
            'product_id' => $product->id,
            'quantity' => 10,
            'date' => '2024-08-02'
        ], $headers);

        $response->assertStatus(201)
                 ->assertJsonStructure(['message', 'transaction']);
    }

    public function test_user_can_update_transaction()
    {
        $headers = $this->authenticate();

        $product = Product::factory()->create();
        $transaction = Transaction::factory()->create([
            'product_id' => $product->id,
            'type' => 'purchase',
            'quantity' => 50,
            'price' => 45.00,
            'date' => '2024-08-01'
        ]);

        $response = $this->putJson("/api/transactions/{$transaction->id}", [
            'quantity' => 60,
            'price' => 50.00
        ], $headers);

        $response->assertStatus(200)
                 ->assertJsonStructure(['message', 'transaction']);
    }

    public function test_user_can_delete_transaction()
    {
        $headers = $this->authenticate();

        $product = Product::factory()->create();
        $transaction = Transaction::factory()->create([
            'product_id' => $product->id,
            'type' => 'purchase',
            'quantity' => 50,
            'price' => 45.00,
            'date' => '2024-08-01'
        ]);

        $response = $this->deleteJson("/api/transactions/{$transaction->id}", [], $headers);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Deleted Transaction Successfully!']);
    }
}
