<?php

namespace App\Services\Methods;

use App\Models\Option;
use App\Models\OptionValue;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\Property;
use App\Models\PropertyValue;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

class Maper
{

    public static function optionsAndProperties(Product $product): void
    {
        self::valuesToKeys($product, 'propertyValues');
        self::valuesToGroups($product, 'optionValues');
    }

    public static function valuesToKeys(Product|ProductType $product, string $relation, bool $group_to_value_id = false): void
    {
        $product->setRelation($relation, Maper::toKeys($product->$relation, $group_to_value_id));
    }

    public static function toKeys(Collection $relation, bool $group_to_value_id): Collection
    {
        return $relation->mapWithKeys(function (OptionValue|PropertyValue $value) use ($group_to_value_id) {
            if ($value instanceof OptionValue) $key = $group_to_value_id ? $value->option_id : $value->option->title;
            else $key = $group_to_value_id ? $value->property_id : $value->property->title;
            return [$key => $value->value];
        }
        );
    }

    public static function valuesToGroups(Product|ProductType $product, string $relation): void
    {
        $product->setRelation($relation, self::toGroups($product->$relation));
    }

    public static function toGroups(Collection $relation): Collection
    {
        return $relation->mapToGroups(function (OptionValue|PropertyValue $value) {
            if ($value instanceof OptionValue) $key = $value->option->title;
            else $key = $value->property->title;
            return [$key => $value->unsetRelations()];
        });
    }

    public static function OptionOrPropertyValues(Collection $optionsOrProperties): Collection
    {
        return $optionsOrProperties->mapWithKeys(function (Option|Property $optionOrProperty) {
            if ($optionOrProperty instanceof Option) $value = $optionOrProperty->optionValues;
            else $value = $optionOrProperty->propertyValues;
            return [$optionOrProperty->title => $value->pluck('value', 'id')];
        });
    }

    public static function mapAfterGettingProducts(Paginator|Collection &$productTypes): void
    {
        $productTypes->map(function (ProductType $productType) {
            self::valuesToGroups($productType, 'optionValues');
            self::countingRatingAndComments($productType->product);
        });
    }

    public static function countingRatingAndComments(Product $product): void
    {
        $product->setAttribute('rating', round(($product->ratingAndComments->avg('rating') ?? 0) * 2) / 2);
        $product->setAttribute('countRating', $product->ratingAndComments->count());
        $product->setAttribute('countComments', $product->ratingAndComments->pluck('message')->filter()->count());
    }
}
