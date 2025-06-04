<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // Import SoftDeletes
use Illuminate\Support\Str; // Untuk order_uid

class Order extends Model
{
    use HasFactory, SoftDeletes; // SoftDeletes

    protected $fillable = [
        'order_uid',
        'user_id',
        'courier_id',
        'total_amount',
        'status',
        'delivery_address',
        'delivery_latitude',
        'delivery_longitude',
        'estimated_delivery_time',
        'actual_delivery_time',
        'notes_customer',
        'payment_method',
        'payment_status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'total_amount' => 'decimal:2',
        'delivery_latitude' => 'decimal:7',
        'delivery_longitude' => 'decimal:7',
        'estimated_delivery_time' => 'datetime',
        'actual_delivery_time' => 'datetime',
    ];

    /**
     * Boot logic for the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_uid)) {
                // Membuat UID yang lebih unik dan sulit ditebak
                // misal ORD-YYYYMMDD-XXXXXX (6 karakter random)
                $prefix = 'ORD-' . now()->format('Ymd') . '-';
                do {
                    $uid = $prefix . strtoupper(Str::random(6));
                } while (static::where('order_uid', $uid)->exists()); // unik
                $order->order_uid = $uid;
            }
            // Set default status jika belum ada
            if (empty($order->status)) {
                $order->status = 'pending_payment';
            }
            if (empty($order->payment_status)) {
                $order->payment_status = 'pending';
            }
        });
    }

    /**
     * Get the customer that placed the order.
     */
    public function customer() // Menggunakan 'customer' agar lebih jelas dari 'user' saja
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the courier assigned to the order.
     */
    public function courier()
    {
        return $this->belongsTo(User::class, 'courier_id');
    }

    /**
     * Get all of the items for the order.
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

}