<?php

namespace App\Services\Admin\Product\Relations;

use App\Components\Method;
use App\Models\OptionValueProduct;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\PropertyValue;
use Illuminate\Support\Collection;

class OptionValueService
{

    public function forRelationsType(Product $product, ProductType $productType, array $relations, bool $isNewProduct): array
    {
        if ($isNewProduct) {
            foreach ($relations['optionValues'] as &$optionValue) {
                $optionValue = ['productType_id' => $productType->id, 'optionValue_id' => $optionValue];
            }
        } else {
            $productType->optionValues()->attach($relations['optionValues']);
            $product->optionValues()->sync($relations['optionValues'], false);
        }
        return $relations;
    }

    public function forRelationsProduct(Product $product, array $detachedOptionValues): void
    {
        $product->productTypes()->whereHas('optionValues', function ($b) use ($detachedOptionValues) {
            $b->whereIn('optionValues.id', $detachedOptionValues);
        })->update(['is_published' => 0]);
    }

    public function detachProductOptionValues(ProductType $productType): void
    {
        OptionValueProduct::query()
            ->where('product_id', $productType->product_id)
            ->whereDoesntHave('optionValues.productTypes', function ($q) use ($productType) {
                $q->where('product_id', $productType->product_id);
            })
            ->delete();
    }

    public function getOptionValues(Product $product): Collection
    {
        $optionValues = $product->optionValues()
            ->select('optionValues.id', 'option_id', 'value')
            ->with('option:id,title')
            ->get();
        return Method::toGroups($optionValues);
    }
}
