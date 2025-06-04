<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // untuk Sanctum

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable; // HasApiTokens untuk API

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', 
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed', // Otomatis hash password saat diset
    ];

    /**
     * Get the orders placed by the user (as a customer).
     */
    public function customerOrders() // Menggunakan nama yang lebih spesifik
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    /**
     * Get the orders handled by the user (as a courier).
     */
    public function courierOrders() // Menggunakan nama yang lebih spesifik
    {
        return $this->hasMany(Order::class, 'courier_id');
    }
}