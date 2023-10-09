<?php

namespace App\Services\Admin\Product\Relations;

use App\Models\OptionValueProduct;
use App\Models\Product;
use App\Models\ProductType;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Support\Collection;

class OptionValueService
{

    /**
     * @param Product $product
     * @param ProductType $productType
     * @param array<int> $optionValues
     * @param bool $isNewProduct
     * @return array{}|array<array<string, int|string>>
     */
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

    /**
     * @param Product $product
     * @param array<int> $detachedOptionValues
     * @return void
     */
    public function unpublishTypesAfterUpdateProduct(Product $product, array $detachedOptionValues): void
    {
        $product->productTypes()
            ->whereHas('optionValues', fn(Builder $b) => $b->whereIn('optionValues.id', $detachedOptionValues))
            ->update(['is_published' => 0]);
    }

    public function detachProductOptionValues(ProductType $productType): void
    {
        OptionValueProduct::query()
            ->where('product_id', $productType->product_id)
            ->whereDoesntHave('optionValues.productTypes', function (Builder $q) use ($productType) {
                $q->where('product_id', $productType->product_id);
            })
            ->delete();
    }

    /**
     * @param Product $product
     * @return Collection<int|string, Collection<int|string, mixed>>
     */
    public function getOptionValues(Product $product): Collection
    {
        /** @phpstan-ignore-next-line */
        return $product->optionValues()->getAndGroupWithParentTitle();
    }
}
