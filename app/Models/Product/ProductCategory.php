<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function getTakeIconAttribute()
    {
        return "/storage/" . $this->icon;
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'product_category_id');
    }
}
