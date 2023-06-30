<?php

namespace App\Services\Product;

use App\Models\Product;
use App\Models\ProductType;
use Illuminate\Support\Facades\DB;

class ProductTypeService
{

    public $imageService;
    public $relationService;

    public function __construct(ImageService $imageService, RelationService $relationService)
    {
        $this->imageService = $imageService;
        $this->relationService = $relationService;
    }


    public function store(Product $product, $type, ?bool $isNewProduct = true): array|string
    {
        DB::beginTransaction();
        try {
            $relations = $this->relationService->getRelations($type);
            $relations['optionValues'] = array_filter($relations['optionValues']);

            $type['product_id'] = $product->id;
            $type['is_published'] =  $type['count'] > 0 ?  $type['is_published'] ?? 0 : 0;
            $type['preview_image'] = $this->imageService->previewImage($type['preview_image']);

            $productType = ProductType::create($type);

            $this->imageService->productImages($productType, $relations['productImages'], $isNewProduct);

            $attach = $this->relationService->relationsType($product, $productType, $relations, $isNewProduct);
            DB::commit();
            return $attach;
        } catch (\Exception $exception) {
            DB::rollBack();
            return $exception->getMessage();
        }
    }


    public function update(ProductType $productType, $type): ?string
    {
        $relations = $this->relationService->getRelations($type);

        DB::beginTransaction();
        try {

            if (!empty($type['preview_image'])) $this->imageService->previewImage($type['preview_image'], $productType->preview_image);
            if (!empty($relations['productImages'])) {
                $productType->load('productImages:id,productType_id,file_path');
                $this->imageService->productImages($productType, $relations['productImages'], false);
            }

            $productType->load('product:id')->update($type);

            $productType->optionValues()->sync($relations['optionValues']);
            $this->relationService->updateProductOVs($productType);
            DB::commit();
            return null;
        } catch (\Exception $exception) {
            DB::rollBack();
            return $exception->getMessage();
        }
    }


    public function delete(ProductType $productType): ?string
    {
        DB::beginTransaction();
        try {

            $productType->load('productImages:productType_id,file_path', 'product:id')->delete();

            $images = $productType->productImages->pluck('file_path')->push($productType->preview_image)->all();
            $this->imageService->deleteImages($images);
            $this->relationService->updateProductOVs($productType);

            DB::commit();
            return null;
        } catch (\Exception $exception) {
            DB::rollBack();
            return $exception->getMessage();
        }
    }
}
