<?php

namespace App\Services\Product;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductTypeOptionValue;
use Illuminate\Support\Facades\DB;


class ProductService
{
    public $productTypeService;

    public function __construct(ProductTypeService $productTypeService)
    {
        $this->productTypeService = $productTypeService;
    }


    public function store($product, $types): ?string
    {
        $relations = $this->productTypeService->relationService->getRelations($product);
        $relations['optionValues'] = array_filter(array_unique(array_merge(...array_column($types, 'optionValues'))));
        DB::beginTransaction();
        try {

            $product = Product::firstOrCreate(['title' => $product['title']], $product);
            $this->productTypeService->relationService->relationsProduct($product, $relations);
            foreach ($types as $type) $attach[] = $this->productTypeService->store($product, $type);

            ProductTypeOptionValue::insert(array_merge(...array_column($attach, 'optionValues')));
            ProductImage::insert(array_merge(...array_column($attach, 'productImages')));

            DB::commit();
            return null;
        } catch (\Exception $exception) {
            DB::rollBack();
            return $exception->getMessage();
        }
    }


    public function update(Product $product, $data, $relations): ?string
    {
        DB::beginTransaction();
        try {

            $product->update($data);
            $this->productTypeService->relationService->relationsProduct($product, $relations, false);

            DB::commit();
            return null;
        } catch (\Exception $exception) {
            DB::rollBack();
            return $exception->getMessage();
        }
    }


    public function delete(Product $product): ?string
    {
        $product->load('productTypes.productImages:productType_id,file_path');
        $images = $product->productTypes->pluck('productImages.*.file_path')->push($product->productTypes->pluck('preview_image'))->flatten()->all();

        DB::beginTransaction();
        try {
            $product->delete();
            $this->productTypeService->imageService->deleteImages($images);
            DB::commit();
            return null;
        } catch (\Exception $exception) {
            DB::rollBack();
            return $exception->getMessage();
        }
    }
}
