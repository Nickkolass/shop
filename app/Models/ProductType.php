<?php

namespace App\Models;

use App\Components\Method;
use App\Models\Traits\Filterable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    public function liked()
    {
        return $this->beLongsToMany(User::class, 'productType_user_likes', 'productType_id', 'user_id');
    }

    public function scopeSorted(Builder $query, $orderBy)
    {
        if ($orderBy == 'rating') {
            $query->leftJoin('products', 'products.id', '=', 'productTypes.product_id')
                ->leftJoin('rating_and_comments', 'products.id', '=', 'rating_and_comments.product_id')
                ->select(array('productTypes.*',
                    DB::raw('AVG(rating) as rating')
                ))
                ->groupBy('id')
                ->orderBy('rating', 'DESC');
        } elseif ($orderBy == 'latest') $query->latest();
        else $query->orderBy('price', $orderBy);
    }
}
