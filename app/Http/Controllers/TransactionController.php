<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Transaction;
use App\Enums\HttpStatus;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    /**
     * Summary of recalculateTransactions
     * @param mixed $productId
     * @return void
     * 
     * Desc : Recalculates the weighted avg cost
     */
    public function recalculateTransactions($productId)
    {
        $product = Product::findOrFail($productId);
        $transactions = Transaction::where('product_id', $productId)
                                    ->orderBy('date', 'asc')
                                    ->get();

        $totalQuantity = 0;
        $totalValue = 0;

        foreach ($transactions as $transaction) {
            if ($transaction->type == 'purchase') {
                $totalQuantity += $transaction->quantity;
                $totalValue += $transaction->quantity * $transaction->price;
                $product->price = $totalValue / $totalQuantity;
            } elseif ($transaction->type == 'sale') {
                $transaction->cost = $product->price * $transaction->quantity;
                $totalQuantity -= $transaction->quantity;
                $totalValue -= $transaction->cost;
            }

            $transaction->save();
        }

        $product->quantity = $totalQuantity;
        $product->save();
    }

    /**
     * Summary of recordSale
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     * 
     * Desc : Records a sale
     */
    public function recordSale(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), HttpStatus::BAD_REQUEST->value);
        }

        // Return error if not enough stock
        $product = Product::find($request->product_id);
        if ($product->quantity < $request->quantity) {
            return response()->json(['error' => 'Not Enough Stock!'], HttpStatus::BAD_REQUEST->value);
        }

        // Create the sale
        $transaction = Transaction::create([
            'product_id' => $request->product_id,
            'type' => 'sale',
            'quantity' => $request->quantity,
            'price' => $product->price,
            'date' => $request->date,
            'cost' => $product->price * $request->quantity,
        ]);

        // Make adjustments and recalculate the WAC
        $this->recalculateTransactions($request->product_id);

        return response()->json([
            'message' => 'Recorded Sale Successfully!',
            'transaction' => $transaction
        ], HttpStatus::CREATED->value);
    }

    /**
     * Summary of recordPurchase
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     * 
     * Desc : Records a purchase
     */
    public function recordPurchase(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), HttpStatus::BAD_REQUEST->value);
        }

        // Record the purchase
        $transaction = Transaction::create([
            'product_id' => $request->product_id,
            'type' => 'purchase',
            'quantity' => $request->quantity,
            'price' => $request->price,
            'date' => $request->date,
            'cost' => null,
        ]);

        // Make adjustments and recalculate the WAC
        $this->recalculateTransactions($request->product_id);

        return response()->json([
            'message' => 'Recorded Purchase Successfully!',
            'transaction' => $transaction
        ], HttpStatus::CREATED->value);
    }

    /**
     * Summary of getSales
     * @return mixed|\Illuminate\Http\JsonResponse
     * 
     * Desc : Gets list of sales or purchases by ascending date
     */
    public function getSalesOrPurchases($type)
    {
        $val = Transaction::where('type', $type)
                            ->with('product')
                            ->orderBy('date', 'asc')
                            ->get();
        
        $val->each(function ($transaction) {
            $hiddenFields = ['product_id'];

            if ($transaction->type === 'purchase') {
                $hiddenFields[] = 'cost';
            }
        
            $transaction->makeHidden($hiddenFields);
        });
                            
        return response()->json($val);
    }

    /**
     * Summary of updateTransaction
     * @param \Illuminate\Http\Request $request
     * @param mixed $id
     * @return mixed|\Illuminate\Http\JsonResponse
     * 
     * Desc : Update the transaction
     */
    public function updateTransaction(Request $request, $id)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'quantity' => 'integer|min:1',
            'price' => 'numeric|min:0',
            'date' => 'date',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), HttpStatus::BAD_REQUEST->value);
        }

        // Find the transaction, or not throw 404
        $transaction = Transaction::findOrFail($id);

        // Update the transaction details
        $transaction->update($request->only('quantity', 'price', 'date'));

        // Make adjustments and recalculate the WAC
        $this->recalculateTransactions($transaction->product_id);

        return response()->json([
            'message' => 'Updated Transaction Successfully!',
            'transaction' => $transaction
        ]);
    }

    /**
     * Summary of deleteTransaction
     * @param mixed $id
     * @return mixed|\Illuminate\Http\JsonResponse
     * 
     * Desc : Delete a transaction
     */
    public function deleteTransaction($id)
    {
        // Find the transaction, if not throw 404
        $transaction = Transaction::findOrFail($id);

        // Delete the transaction
        $transaction->delete();

        // Make adjustments and recalculate the WAC
        $this->recalculateTransactions($transaction->product_id);

        return response()->json([
            'message' => 'Deleted Transaction Successfully!'
        ]);
    }
}
