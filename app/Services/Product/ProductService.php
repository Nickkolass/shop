<?php

namespace App\Services\Product;

use App\Models\Product;
use Illuminate\Support\Facades\DB;


class ProductService
{
    public $productTypeService;

    public function __construct(ProductTypeService $productTypeService)
    {
        $this->productTypeService = $productTypeService;
    }


    public function store($product, $types)
    {
        $sync = $this->productTypeService->relationService->getSync($product);
        $sync['optionValues'] = array_merge(...array_column($types, 'optionValues'));
        DB::beginTransaction();
        try {

            $product = Product::firstOrCreate([
                'title' => $product['title']
            ], $product);

            $this->productTypeService->relationService->sync($product, $sync);

            foreach ($types as $type) {
                $type['is_published'] =  $type['count'] > 0 ?  $type['is_published'] ?? 0 : 0; 
                $this->productTypeService->storeType($product, $type);
            }

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return $exception->getMessage();
        }
    }


    public function update(Product $product, $data, $sync)
    {
        DB::beginTransaction();
        try {

            $product->update($data);
            $this->productTypeService->relationService->sync($product, $sync);

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return $exception->getMessage();
        }
    }


    public function delete(Product $product)
    {
        $product->load(['productTypes' => function ($q) {
            $q->select('id', 'product_id', 'preview_image')->with('productImages:productType_id,file_path');
        }]);

        $images = $product->productTypes->pluck('productImages.*.file_path')->push($product->productTypes->pluck('preview_image'))->flatten();

        DB::beginTransaction();
        try {
            $this->productTypeService->imageService->deleteImages($images);
            $product->delete();
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return $exception->getMessage();
        }
    }
}
