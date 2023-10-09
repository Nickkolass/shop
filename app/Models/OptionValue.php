<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property int $option_id
 * @property string $value
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property ?Option $option
 * @property ?Collection<Product> $products
 * @property ?Collection<ProductType> $productTypes
 * @method static Collection getAndGroupWithParentTitle()
 * @method static static|Builder selectParentTitle()
 */
class OptionValue extends Model
{
    use HasFactory;

    protected $table = 'optionValues';
    protected $guarded = false;
    protected $hidden = ['created_at', 'updated_at', 'pivot'];

    public function option(): BelongsTo
    {
        return $this->beLongsTo(Option::class, 'option_id', 'id');
    }

    public function products(): BelongsToMany
    {
        return $this->beLongsToMany(Product::class, 'optionValue_products', 'optionValue_id', 'product_id');
    }

    public function productTypes(): BelongsToMany
    {
        return $this->beLongsToMany(ProductType::class, 'productType_optionValues', 'optionValue_id', 'productType_id');
    }

    /**
     * @param Builder $b
     * @return Collection<int|string, Collection<int|string, mixed>>
     */
    public function scopeGetAndGroupWithParentTitle(Builder $b): Collection
    {
        /** @phpstan-ignore-next-line */
        return $b->select('optionValues.id', 'option_id', 'value')
            ->selectParentTitle()
            ->toBase()
            ->get()
            ->groupBy('option_title');
    }

    public function scopeSelectParentTitle(Builder $b): void
    {
        $b->selectSub(function (Builder $q) {
            $q->from('options')
                ->whereColumn('options.id', 'option_id')
                ->select('options.title');
        }, 'option_title');
    }
}
