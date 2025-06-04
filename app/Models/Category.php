<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str; // Import Str untuk slug

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    /**
     * Get the products for the category.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Boot logic for the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('name') && empty($category->slug)) { // Hanya jika nama berubah dan slug kosong
                 $category->slug = Str::slug($category->name);
            } elseif ($category->isDirty('name') && !empty($category->getOriginal('slug')) && $category->slug === $category->getOriginal('slug')) {
                // Jika nama berubah, dan slug tidak diisi manual, maka update slug juga
                // mencegah slug terupdate jika user mengedit slug secara manual
                $category->slug = Str::slug($category->name);
            }
        });
    }

    /**
     * Get the route key for the model.
     * Untuk menggunakan 'slug' dalam Route Model Binding secara default.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }
}