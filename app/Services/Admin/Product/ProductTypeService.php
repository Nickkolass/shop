<?php

namespace App\Services\Admin\Product;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductType;
use App\Services\Admin\Product\Relations\RelationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductTypeService
{

    public ImageService $imageService;
    public RelationService $relationService;

    public function __construct(ImageService $imageService, RelationService $relationService)
    {
        $this->imageService = $imageService;
        $this->relationService = $relationService;
    }

    /**
     * storeType для устранения повторного запуска транзакции при создании продукта (напрямую дергается метод storeType)
     *
     */

    public function store(Product $product, array $type): ?string
    {
        DB::beginTransaction();
        try {
            $attach = $this->storeType($product, $type);
            DB::commit();
            return null;
        } catch (\Exception $exception) {
            DB::rollBack();
            if(!empty($attach['productImages'])) $this->imageService->deleteImages(array_column($attach['productImages'], 'file_path'));
            return $exception->getMessage();
        }
    }

    public function storeType(Product $product, array $type, ?bool $isNewProduct = false): array
    {
        $relations = $this->relationService->getRelations($type);
        $relations['optionValues'] = array_filter($relations['optionValues']);

        $type['product_id'] = $product->id;
        $type['is_published'] = $type['count'] > 0 ? $type['is_published'] ?? 0 : 0;
        $type['preview_image'] = $this->imageService->previewImage($type['preview_image'], $product->id);

        $productType = ProductType::create($type);
        $this->imageService->productImages($productType, $relations['productImages'], $isNewProduct);
        $attach = $this->relationService->relationsType($product, $productType, $relations, $isNewProduct);
        return $attach;
    }

    public function update(ProductType $productType, array $type): ?string
    {
        DB::beginTransaction();
        try {
            $relations = $this->relationService->getRelations($type);

            if (!empty($relations['productImages'])) {
                $productType->load('productImages:id,productType_id,file_path');
                $old_image_paths = $productType->productImages->pluck('file_path')->all();
                $this->imageService->productImages($productType, $relations['productImages'], false);
                $new_image_paths = array_column($relations['productImages'], 'file_path');
            }

            if (!empty($type['preview_image'])) {
                $type['preview_image'] = $new_image_paths[] = $this->imageService->previewImage($type['preview_image'], $productType->product_id);
                $old_image_paths[] = $productType->preview_image;
            }

            $productType->update($type);

            $productType->optionValues()->sync($relations['optionValues']);
            $this->relationService->optionValueService->detachProductOptionValues($productType);
            DB::commit();
            if(isset($old_image_paths)) $this->imageService->deleteImages($old_image_paths);
            return null;
        } catch (\Exception $exception) {
            DB::rollBack();
            if(isset($new_image_paths)) $this->imageService->deleteImages($new_image_paths);
            return $exception->getMessage();
        }
    }

    public function delete(ProductType $productType): ?string
    {
        DB::beginTransaction();
        try {

            $productType->load('productImages:productType_id,file_path')->delete();

            $images = $productType->productImages->pluck('file_path')->push($productType->preview_image)->all();
            $this->relationService->optionValueService->detachProductOptionValues($productType);
            $this->imageService->deleteImages($images);

            DB::commit();
            return null;
        } catch (\Exception $exception) {
            DB::rollBack();
            return $exception->getMessage();
        }
    }
}
