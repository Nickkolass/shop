<?php

namespace App\Models;

use App\Http\Filters\FilterInterface;
use App\Models\Traits\Filterable;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property int $product_id
 * @property int $price
 * @property int $count
 * @property int $count_likes
 * @property bool $is_published
 * @property string $preview_image
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property ?Product $product
 * @property ?Category $category
 * @property ?User $saler
 * @property null|Collection<OptionValue>|Collection<string, string> $optionValues
 * @property ?Collection<ProductImage> $productImages
 * @property ?Collection<ProductType> $liked
 * @method static static|Builder sort(string $orderBy)
 * @method static static|Builder filter(FilterInterface $filter)
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

    public function scopeSort(Builder $b, string $orderBy): void
    {
        if ($orderBy == 'rating') {
            $b->orderBy(function (Builder $q) {
                $q->from('products')
                    ->whereColumn('productTypes.product_id', '=', 'products.id')
                    ->selectRaw('AVG(rating)');
            });
        } elseif ($orderBy == 'latest') $b->latest();
        else $b->orderBy('price', $orderBy);
    }
}
