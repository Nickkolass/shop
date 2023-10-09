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
 * @property int $property_id
 * @property string $value
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property ?Property $property
 * @property ?Collection<Product> $products
 * @method static Collection getAndGroupWithParentTitle()
 * @method static static|Builder selectParentTitle()
 */
class PropertyValue extends Model
{
    use HasFactory;

    protected $table = 'property_values';
    protected $guarded = false;
    protected $hidden = ['created_at', 'updated_at', 'pivot'];

    public function property(): BelongsTo
    {
        return $this->beLongsTo(Property::class, 'property_id', 'id');
    }

    public function products(): BelongsToMany
    {
        return $this->beLongsToMany(Product::class, 'property_value_products', 'property_value_id', 'product_id');
    }

    /**
     * @param Builder $b
     * @return Collection<int|string, Collection<int|string, mixed>>
     */
    public function scopeGetAndGroupWithParentTitle(Builder $b): Collection
    {
        /** @phpstan-ignore-next-line */
        return $b->select('property_values.id', 'property_id', 'value')
            ->selectParentTitle()
            ->toBase()
            ->get()
            ->groupBy('property_title');
    }

    public function scopeSelectParentTitle(Builder $b): void
    {
        $b->selectSub(function (Builder $q) {
            $q->from('properties')
                ->whereColumn('properties.id', 'property_id')
                ->select('properties.title');
        }, 'property_title');
    }
}
