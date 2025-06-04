<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name', 
        'quantity',
        'price_at_order',
        'sub_total',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'price_at_order' => 'decimal:2',
        'sub_total' => 'decimal:2',
        'quantity' => 'integer',
    ];

    /**
     * Indicates if the model should be timestamped.
     * Sesuai migrasi, OrderItem tidak memiliki timestamps sendiri,
     */

    public $timestamps = false; // Sesuai desain migrasi 

    /**
     * Get the order that owns the order item.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the product associated with the order item.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}