<?php

namespace App\Services\Product;

use App\Models\Product;
use App\Models\ProductType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductTypeService
{

    public $imageService;
    public $relationService;

    public function __construct(ImageService $imageService, RelationService $relationService)
    {
        $this->imageService = $imageService;
        $this->relationService = $relationService;
    }


    public function storeType(Product $product, $type, ?bool $isNewProduct = true)
    {
        DB::beginTransaction();
        try {
            
            $sync = $this->relationService->getSync($type);
            $type['product_id'] = $product->id;

            $this->imageService->previewImage($type['preview_image']);

            $productType = ProductType::create($type);

            $this->imageService->productImages($productType, $sync['productImages']);

            if ($isNewProduct) {
                $productType->optionValues()->attach($sync['optionValues']);
            } else {
                $productType->optionValues()->sync($sync['optionValues'], false);
                $product->optionValues()->sync($sync['optionValues'], false);
            }

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return $exception->getMessage();
        }
    }


    public function updateType(ProductType $productType, $type)
    {
        $sync = $this->relationService->getSync($type);

        DB::beginTransaction();
        try {

            if (!empty($sync['productImages'])) {
                $productType->load('productImages:productType_id,file_path');
                $this->imageService->productImages($productType, $sync['productImages'], true);
            }
            empty($type['preview_image']) ?: $this->imageService->previewImage($type['preview_image'], $productType->preview_image);

            $productType->update($type);
            $productType->optionValues()->sync($sync['optionValues']);
            $this->relationService->updateProductOVs($productType, $sync['optionValues']);

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return $exception->getMessage();
        }
    }


    public function deleteType(ProductType $productType)
    {
        DB::beginTransaction();
        try {

            $productType->load('productImages:productType_id,file_path', 'optionValues:optionValues.id');
            $images = $productType->productImages->pluck('file_path')->push($productType->preview_image);

            $this->imageService->deleteImages($images);
            $productType->delete();
            $this->relationService->updateProductOVs($productType, $productType->optionValues->pluck('id'));

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return $exception->getMessage();
        }
    }
}
