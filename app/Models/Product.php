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
use Illuminate\Support\Collection;
use Laravel\Scout\Attributes\SearchUsingFullText;
use Laravel\Scout\Builder as ScoutBuilder;
use Laravel\Scout\Searchable;

/**
 * @property int $id
 * @property int $saler_id
 * @property int $category_id
 * @property string $title
 * @property string $description
 * @property int $rating
 * @property int $count_rating
 * @property int $count_comments
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property ?User $saler
 * @property ?Category $category
 * @property ?Collection<ProductType> $productTypes
 * @property ?Collection<RatingAndComment> $ratingAndComments
 * @property ?Collection<Tag> $tags
 * @property ?Collection<PropertyValue> $propertyValues
 * @property null|Collection<OptionValue>|Collection<string, string> $optionValues
 * @method static static|Builder filter(FilterInterface $filter)
 * @method static ScoutBuilder search(string $search)
 */
class Product extends Model
{

    use HasFactory, Searchable, Filterable;

    protected $table = 'products';
    protected $guarded = false;
    protected $hidden = ['pivot'];

    public function productTypes(): HasMany
    {
        return $this->hasMany(ProductType::class, 'product_id', 'id');
    }

    public function ratingAndComments(): HasMany
    {
        return $this->hasMany(RatingAndComment::class, 'product_id', 'id');
    }

    public function tags(): BelongsToMany
    {
        return $this->beLongsToMany(Tag::class, 'product_tags', 'product_id', 'tag_id');
    }

    public function saler(): BelongsTo
    {
        return $this->beLongsTo(User::class, 'saler_id', 'id');
    }

    public function category(): BelongsTo
    {
        return $this->beLongsTo(Category::class, 'category_id', 'id');
    }

    public function propertyValues(): BelongsToMany
    {
        return $this->beLongsToMany(PropertyValue::class, 'property_value_products', 'product_id', 'property_value_id');
    }

    public function optionValues(): BelongsToMany
    {
        return $this->beLongsToMany(OptionValue::class, 'optionValue_products', 'product_id', 'optionValue_id');
    }

    /**
     * @return array<string, string>
     */
//    #[SearchUsingPrefix()]
    #[SearchUsingFullText(['title', 'description'])]
    public function toSearchableArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
        ];
    }
}
