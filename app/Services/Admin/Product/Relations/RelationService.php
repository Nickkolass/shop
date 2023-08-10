<?php

namespace App\Services\Admin\Product\Relations;

use App\Models\Product;
use App\Models\ProductType;

class RelationService
{

    public OptionValueService $optionValueService;
    public PropertyValueService $propertyValueService;

    public function __construct(OptionValueService $optionValueService, PropertyValueService $propertyValueService)
    {
        $this->optionValueService = $optionValueService;
        $this->propertyValueService = $propertyValueService;
    }

    public function getRelations(Product|ProductType|array &$product): array
    {
        foreach ($product as $relation => $keys) {
            if (is_array($keys)) {
                $res[$relation] = $keys;
                unset($product[$relation]);
            }
        }
        return $res ?? [];
    }

    public function relationsType(Product $product, ProductType $productType, array $relations, bool $isNewProduct): array
    {
        return $this->optionValueService->forRelationsType($product, $productType, $relations, $isNewProduct);
    }

    public function relationsProduct(Product $product, array $relations, ?bool $isNewProduct = true): void
    {
        $this->propertyValueService->forRelationsProduct($relations['propertyValues']);

        if ($isNewProduct) foreach ($relations as $relation => $keys) $product->$relation()->attach($keys);
        else foreach ($relations as $relation => $keys) $detached[$relation] = $product->$relation()->sync($keys)['detached'];

        if ($detached['optionValues'] ?? false) $this->optionValueService->forRelationsProduct($product, $detached['optionValues']);
    }
}
