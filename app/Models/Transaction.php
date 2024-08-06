<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $fillable = [ // Allow mass assignment
        'product_id',
        'type',
        'quantity',
        'price',
        'cost',
        'date',
    ];
    
    public function product()
    {
        return $this->belongsTo(Product::class);     // To define that a transaction belongs to a product.
    }
}
