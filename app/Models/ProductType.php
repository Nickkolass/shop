<?php

namespace App\Models;

use App\Models\Traits\Filterable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductType extends Model
{
    use HasFactory, Filterable;
    
    protected $table = 'productTypes';
    protected $guarded = false;


    public function product()
    {
        return $this->beLongsTo(Product::class, 'product_id', 'id');
    }

    public function category()
    {
        return $this->hasOneThrough(Category::class, Product::class, 'id', 'id', 'product_id', 'category_id');
    }

    public function saler()
    {
        return $this->hasOneThrough(User::class, Product::class, 'id', 'id', 'product_id', 'saler_id');
    }

    public function optionValues()
    {
        return $this->beLongsToMany(OptionValue::class, 'productType_optionValues', 'productType_id', 'optionValue_id');
    }

    public function productImages()
    {
        return $this->hasMany(ProductImage::class, 'productType_id', 'id');
    }

    public function scopeSorted(Builder $query, $orderBy)
    {
        $orderBy == 'latest' ? $query->latest() : $query->orderBy('price', $orderBy);
    }

}
