<?php

namespace App\Services\Admin\Product\Relations;

use App\Dto\Admin\Product\ProductRelationDto;
use App\Dto\Admin\Product\ProductTypeRelationDto;
use App\Dto\Admin\Product\ProductTypeRelationForInsertDto;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductType;
use App\Models\ProductTypeOptionValue;
use App\Services\Admin\Product\ImageService;
use Illuminate\Support\Collection;

class RelationService
{

    public function __construct(
        public ImageService         $imageService,
        public OptionValueService   $optionValueService,
        public PropertyValueService $propertyValueService,
    )
    {
    }

    public function createRelationsProduct(Product $product, ProductRelationDto $productRelationDto, bool $isNewProduct): void
    {
        $this->propertyValueService->upsertPropertyValues($productRelationDto->propertyValues);
        if ($isNewProduct) foreach ((array)$productRelationDto as $relation => $keys) $product->$relation()->attach($keys);
        else {
            $detached = [];
            foreach ((array)$productRelationDto as $relation => $keys) $detached[$relation] = $product->$relation()->sync($keys)['detached'];
            if (!empty($detached['optionValues'])) $this->optionValueService->unpublishTypesAfterUpdateProduct($product, $detached['optionValues']);
        }
    }

    public function createRelationsProductType(Product $product, ProductType $productType, ProductTypeRelationDto $productTypeRelationDto, bool $isNewProduct): ProductTypeRelationForInsertDto
    {
        $productImages = $this->imageService->prepareOrCreateProductImages($productType, $productTypeRelationDto->productImages, $isNewProduct);
        $optionValues = $this->optionValueService->prepareOrAttachOptionValues($product, $productType, $productTypeRelationDto->optionValues, $isNewProduct);
        return new ProductTypeRelationForInsertDto($optionValues, $productImages);
    }

    /**
     * @param Collection<int, ProductTypeRelationForInsertDto> $collectionProductTypeRelationsForInsertDto
     * @return void
     */
    public function createRelationsProductTypes(Collection $collectionProductTypeRelationsForInsertDto): void
    {
        ProductTypeOptionValue::query()->insert($collectionProductTypeRelationsForInsertDto->pluck('optionValues')->flatten(1)->all());
        ProductImage::query()->insert($collectionProductTypeRelationsForInsertDto->pluck('productImages')->flatten(1)->all());
    }
}
