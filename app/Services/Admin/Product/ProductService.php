<?php

namespace App\Services\Admin\Product;

use App\Components\Method;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductType;
use App\Models\ProductTypeOptionValue;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class ProductService
{
    public ProductTypeService $productTypeService;

    public function __construct(ProductTypeService $productTypeService)
    {
        $this->productTypeService = $productTypeService;
    }

    public function index(): Paginator
    {
        $user = session('user');
        $products = Product::query()
            ->when($user['role'] != 'admin', function ($q) use ($user) {
                $q->whereHas('saler', function ($q) use ($user) {
                    $q->where('id', $user['id']);
                });
            })
            ->select('id', 'title', 'saler_id', 'category_id')
            ->latest()
            ->with([
                'category:id,title_rus',
                'productTypes:id,product_id,preview_image',
                'ratingAndComments'
            ])
            ->simplePaginate(4);

        $products->map(fn(Product $product) => Method::countingRatingAndComments($product));
        return $products;
    }

    public function store($product, $types): int|string
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
            return $product->id;
        } catch (\Exception $exception) {
            DB::rollBack();
            return $exception->getMessage();
        }
    }

    public function show(Product &$product): void
    {
        $product->load([
            'category:id,title,title_rus',
            'propertyValues.property:id,title',
            'optionValues.option:id,title',
            'tags:id,title',
            'ratingAndComments' => function ($q) {
                $q->with([
                    'user:id,name',
                    'commentImages:comment_id,file_path'
                ]);
            },
            'productTypes' => function ($q) {
                $q->select('id', 'product_id', 'count', 'price', 'is_published', 'preview_image')
                    ->with([
                        'productImages:productType_id,file_path',
                        'optionValues.option:id,title'
                    ])
                    ->withCount('liked');
            }
        ]);

        Method::countingRatingAndComments($product);
        Method::optionsAndProperties($product);
        $product->productTypes->map(fn(ProductType $productType) => Method::valuesToKeys($productType, 'optionValues'));
    }


    public function update(Product $product, array $data, array $relations): ?string
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
        $images = $product
            ->load('productTypes.productImages:productType_id,file_path')
            ->productTypes
            ->pluck('productImages.*.file_path')
            ->push($product->productTypes->pluck('preview_image'))
            ->flatten()
            ->all();

        DB::beginTransaction();
        try {

            $product->delete();
            $this->productTypeService->imageService->deleteImages($images);
            Storage::deleteDirectory('/public/product_images/' . $product->id);

            DB::commit();
            return null;
        } catch (\Exception $exception) {
            DB::rollBack();
            return $exception->getMessage();
        }
    }

    public function publish(Product $product): void
    {
        $product->load('optionValues:id')
            ->productTypes()
            ->whereHas('optionValues', function ($oV) use ($product) {
                $oV->whereIn('optionValues.id', $product->optionValues);
            })
            ->where('count', '!=', 0)
            ->update(['is_published' => request()->has('publish') ? 1 : 0]);
    }
}
