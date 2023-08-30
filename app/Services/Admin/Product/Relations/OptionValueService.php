<?php

namespace App\Services\Admin\Product\Relations;

use App\Models\OptionValueProduct;
use App\Models\Product;
use App\Models\ProductType;
use App\Services\Methods\Maper;
use Illuminate\Support\Collection;

class OptionValueService
{

    public function prepareOrAttachOptionValues(Product $product, ProductType $productType, array $optionValues, bool $isNewProduct): array
    {
        if (!$isNewProduct) {
            $productType->optionValues()->attach($optionValues);
            $product->optionValues()->sync($optionValues, false);
        } else foreach ($optionValues as $optionValue) {
            $optionValuesForInsert[] = ['productType_id' => $productType->id, 'optionValue_id' => $optionValue];
        }
        return $optionValuesForInsert ?? [];
    }

    public function unpublishTypesAfterUpdateProduct(Product $product, array $detachedOptionValues): void
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
        return Maper::toGroups($optionValues);
    }
}
