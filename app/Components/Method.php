<?php

namespace App\Components;

use App\Models\Option;
use App\Models\OptionValue;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\Property;
use App\Models\PropertyValue;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

class Method
{

    public static function optionsAndProperties(Product &$product): void
    {
        Method::valuesToKeys($product, 'propertyValues');
        Method::valuesToGroups($product, 'optionValues');
    }

    public static function valuesToKeys(Product|ProductType &$product, string $relation, ?bool $group_to_value_id = false): void
    {
        $product->setRelation($relation, Method::toKeys($product->$relation, $group_to_value_id));
    }

    public static function toKeys(Collection $relation, ?bool $group_to_value_id = false): Collection
    {
        return $relation->mapWithKeys(function (OptionValue|PropertyValue $value) use ($group_to_value_id) {
                $key = !empty($group_to_value_id) ? $value->option_id ?? $value->property_id : $value->option->title ?? $value->property->title;
                return [$key => $value->value];
            }
        );
    }

    public static function valuesToGroups(Product|ProductType &$product, string $relation): void
    {
        $product->setRelation($relation, Method::toGroups($product->$relation));
    }

    public static function toGroups(Collection $relation): Collection
    {
        return $relation->mapToGroups(function (OptionValue|PropertyValue $value) {
            return [$value->option->title ?? $value->property->title => $value->unsetRelations()];
        });
    }

    public static function OptionOrPropertyValues(Collection $optionOrPropertyValues): Collection
    {
        return $optionOrPropertyValues->mapWithKeys(function (Option|Property $optionOrPropertyValue) {
            $value = $optionOrPropertyValue->optionValues ?? $optionOrPropertyValue->propertyValues;
            return [$optionOrPropertyValue->title => $value->pluck('value', 'id')];
        });
    }

    public static function mapAfterGettingProducts(Paginator|Collection &$productTypes): void
    {
        $productTypes->map(function (ProductType $productType) {
            Method::valuesToGroups($productType, 'optionValues');
            Method::countingRatingAndComments($productType->product);
        });
    }

    public static function countingRatingAndComments(Product &$product): void
    {
        $product->rating = round(($product->ratingAndComments->avg('rating') ?? 0)*2)/2;
        $product->countRating = $product->ratingAndComments->count();
        $product->countComments = $product->ratingAndComments->pluck('message')->filter()->count();
    }
}
