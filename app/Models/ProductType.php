<?php

namespace App\Models;

use App\Http\Filters\FilterInterface;
use App\Models\Traits\Filterable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Support\Facades\DB;

/**
 * @property int id
 * @property int product_id
 * @property int price
 * @property int count
 * @property bool is_published
 * @property string preview_image
 * @property Carbon created_at
 * @property Carbon updated_at
 * @method static self|Builder query()
 * @method self|Builder sort(string $orderBy)
 * @method self|Builder filter(FilterInterface $filter)
 */

class ProductType extends Model
{
    use HasFactory, Filterable;

    protected $table = 'productTypes';
    protected $guarded = false;

    public function product(): BelongsTo
    {
        return $this->beLongsTo(Product::class, 'product_id', 'id');
    }

    public function category(): HasOneThrough
    {
        return $this->hasOneThrough(Category::class, Product::class, 'id', 'id', 'product_id', 'category_id');
    }

    public function saler(): HasOneThrough
    {
        return $this->hasOneThrough(User::class, Product::class, 'id', 'id', 'product_id', 'saler_id');
    }

    public function optionValues(): BelongsToMany
    {
        return $this->beLongsToMany(OptionValue::class, 'productType_optionValues', 'productType_id', 'optionValue_id');
    }

    public function productImages(): HasMany
    {
        return $this->hasMany(ProductImage::class, 'productType_id', 'id');
    }

    public function liked(): BelongsToMany
    {
        return $this->beLongsToMany(User::class, 'productType_user_likes', 'productType_id', 'user_id');
    }

    public function scopeSort(Builder $query, $orderBy): void
    {
        if ($orderBy == 'rating') {
            $query->leftJoin('products', 'products.id', '=', 'productTypes.product_id')
                ->leftJoin('rating_and_comments', 'products.id', '=', 'rating_and_comments.product_id')
                ->addSelect(array('productTypes.*',
                    DB::raw('AVG(rating) as rating')
                ))
                ->groupBy('id')
                ->orderBy('rating', 'DESC');
        } elseif ($orderBy == 'latest') $query->latest();
        else $query->orderBy('price', $orderBy);
    }
}
