<?php

namespace App\Services\Admin\Product\Relations;

use App\Models\Product;
use App\Models\ProductType;


class RelationService
{

    public OptionValueService $OVService;
    public PropertyValueService $PVService;

    public function __construct(OptionValueService $OVService, PropertyValueService $PVService)
    {
        $this->OVService = $OVService;
        $this->PVService = $PVService;
    }

    public function getRelations(mixed &$product): array
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
        return $this->OVService->forRelationsType($product, $productType, $relations, $isNewProduct);
    }

    public function relationsProduct(Product $product, array $relations, ?bool $isNewProduct = true): void
    {
        $this->PVService->forRelationsProduct($relations['propertyValues']);

        if ($isNewProduct) foreach ($relations as $relation => $keys) $product->$relation()->attach($keys);
        else foreach ($relations as $relation => $keys) $detached[$relation] = $product->$relation()->sync($keys)['detached'];

        if ($detached['optionValues'] ?? false) $this->OVService->forRelationsProduct($product, $detached['optionValues']);
    }
}
