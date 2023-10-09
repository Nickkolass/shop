<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property string $title
 * @property string $title_rus
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property ?Collection<Product> $products
 * @property ?Collection<ProductType> $productTypes
 * @property ?Collection<Property> $properties
 */
class Category extends Model
{

    protected $table = 'categories';
    protected $guarded = false;

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id', 'id');
    }

    public function productTypes(): HasManyThrough
    {
        return $this->hasManyThrough(ProductType::class, Product::class);
    }

    public function properties(): BelongsToMany
    {
        return $this->beLongsToMany(Property::class, 'category_properties', 'category_id', 'property_id');
    }
}
