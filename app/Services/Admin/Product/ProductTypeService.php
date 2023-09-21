<?php

namespace App\Services\Admin\Product;

use App\Dto\Admin\Product\ProductTypeRelationForInsertDto;
use App\Dto\Admin\Product\ProductTypeDto;
use App\Exceptions\Admin\ProductException;
use App\Models\Product;
use App\Models\ProductType;
use App\Services\Admin\Product\Relations\RelationService;
use Illuminate\Support\Facades\DB;

class ProductTypeService
{


    public function __construct (public RelationService $relationService)
    {
    }

    /** storeType для недопущения повторного запуска транзакции при создании продукта (напрямую дергается метод storeType) */
    public function store(Product $product, ProductTypeDto $productTypeDto): void
    {
        DB::beginTransaction();
        try {
            $this->storeType($product, $productTypeDto);
            DB::commit();
        } catch (\Throwable $e) {
            ProductException::failedStoreProductOrType($e);
        }
    }

    public function storeType(Product $product, ProductTypeDto $productTypeDto, ?bool $isNewProduct = false): ProductTypeRelationForInsertDto
    {
        $this->relationService->imageService->createPreviewImage($productTypeDto->preview_image, $product->id);

        $productTypeRelationDto = $productTypeDto->productTypeRelationDto;
        unset($productTypeDto->productTypeRelationDto);

        $productType = ProductType::create((array) $productTypeDto + ['product_id' => $product->id]);
        return $this->relationService->createRelationsProductType($product, $productType, $productTypeRelationDto, $isNewProduct);
    }

    public function update(ProductType $productType, ProductTypeDto $productTypeDto): void
    {
        DB::beginTransaction();
        try {
            if (!empty($productTypeDto->productTypeRelationDto->productImages)) {
                $productType->load('productImages:id,productType_id,file_path');
                $old_image_paths = $productType->productImages->pluck('file_path')->all();
                $this->relationService->imageService->prepareOrCreateProductImages($productType, $productTypeDto->productTypeRelationDto->productImages, false);
            }
            if (!empty($productTypeDto->preview_image)) {
                $this->relationService->imageService->createPreviewImage($productTypeDto->preview_image, $productType->product_id);
                $old_image_paths[] = $productType->preview_image;
            }
            $productType->optionValues()->sync($productTypeDto->productTypeRelationDto->optionValues);
            $this->relationService->optionValueService->detachProductOptionValues($productType);
            unset($productTypeDto->productTypeRelationDto);
            $productType->update(array_filter((array) $productTypeDto));

            if (isset($old_image_paths)) $this->relationService->imageService->deleteImages($old_image_paths);
            DB::commit();
        } catch (\Throwable $e) {
            ProductException::failedStoreProductOrType($e);
        }
    }

    public function delete(ProductType $productType): void
    {
        DB::beginTransaction();
        $productType->load('productImages:productType_id,file_path')->delete();
        $image_paths = $productType->productImages->pluck('file_path')->push($productType->preview_image)->all();
        $this->relationService->optionValueService->detachProductOptionValues($productType);
        $this->relationService->imageService->deleteImages($image_paths);
        DB::commit();
    }
}
