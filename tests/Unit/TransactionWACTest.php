<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Product;
use App\Models\Transaction;
use App\Http\Controllers\TransactionController;

class TransactionWACTest extends TestCase
{
    use RefreshDatabase;

    public function test_wac_calculation()
    {   
        // Create product
        $product = Product::factory()->create([
            'name' => 'Test Product',
            'price' => 100.00, 
            'quantity' => 0,
        ]);

        // Create purchase on future date
        $transaction1 = Transaction::factory()->create([
            'product_id' => $product->id,
            'type' => 'purchase',
            'quantity' => 50,
            'price' => 10.00,
            'date' => '2024-08-02'
        ]);

        // Create purchase on earlier date
        $transaction2 = Transaction::factory()->create([
            'product_id' => $product->id,
            'type' => 'purchase',
            'quantity' => 100,
            'price' => 20.00,
            'date' => '2024-08-01'
        ]);

        $controller = new TransactionController();
        $controller->recalculateTransactions($product->id);

        $product->refresh(); // Get updated values
        $this->assertEquals(16.67, round($product->price, 2)); // (100*20 + 50*10) / 150 = 16.67

        // Test to see if cost is correct
        $transaction3 = Transaction::factory()->create([
            'product_id' => $product->id,
            'type' => 'sale',
            'quantity' => 20,
            'date' => '2024-08-03'
        ]);

        $controller->recalculateTransactions($product->id);
        $transaction3->refresh(); // Refresh again

        $this->assertEquals(333.33, round($transaction3->cost, 2)); // 20 * 16.67 = 333.33
    }
}