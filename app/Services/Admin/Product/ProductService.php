<?php

namespace App\Services\Admin\Product;

use App\Dto\Admin\Product\ProductDto;
use App\Dto\Admin\Product\ProductRelationDto;
use App\Exceptions\ProductImageException;
use App\Models\Product;
use App\Models\ProductType;
use App\Services\Methods\Maper;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductService
{

    public function __construct(public readonly ProductTypeService $productTypeService)
    {
    }

    public function index(): Paginator
    {
        $user = session('user');
        $products = Product::query()
            ->when($user['role'] != 'admin', function ($q) use ($user) {
                $q->whereHas('saler', fn($q) => $q->where('id', $user['id']));
            })
            ->select('id', 'title', 'saler_id', 'category_id')
            ->latest()
            ->with([
                'category:id,title_rus',
                'productTypes:id,product_id,preview_image',
                'ratingAndComments'
            ])
            ->simplePaginate(4);
        $products->getCollection()->map(fn(Product $product) => Maper::countingRatingAndComments($product));
        return $products;
    }

    public function store(
        ProductDto         $productDto,
        ProductRelationDto $productRelationDto,
        Collection         $collectionProductTypeDto,
    ): int
    {
        DB::beginTransaction();
        try {
            $product = Product::firstOrCreate(['title' => $productDto->title], (array)$productDto);
            $this->productTypeService->relationService->createRelationsProduct($product, $productRelationDto);

            $productTypeRelationsForInsertDto = collect();
            foreach ($collectionProductTypeDto as $productTypeDto) {
                $productTypeRelationsForInsertDto->push($this->productTypeService->storeType($product, $productTypeDto, true));
            }
            $this->productTypeService->relationService->createRelationsProductTypes($productTypeRelationsForInsertDto);
            DB::commit();
        } catch (\Throwable $e) {
            ProductImageException::failedStoreProduct($e, $product->id);
        }
        return $product->id;
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

        Maper::countingRatingAndComments($product);
        Maper::optionsAndProperties($product);
        $product->productTypes->map(fn(ProductType $productType) => Maper::valuesToKeys($productType, 'optionValues'));
    }

    public function update(Product $product, ProductDto $productDto, ProductRelationDto $productRelationDto): void
    {
        DB::beginTransaction();
        $product->update((array) $productDto);
        $this->productTypeService->relationService->createRelationsProduct($product, $productRelationDto, false);
        DB::commit();
    }

    public function delete(Product $product): void
    {
        $images = $product
            ->load(['productTypes.productImages:productType_id,file_path', 'ratingAndComments.commentImages'])
            ->productTypes
            ->pluck('productImages.*.file_path')
            ->push($product->productTypes->pluck('preview_image'))
            ->push($product->ratingAndComments->pluck('commentImages.*.file_path'))
            ->flatten()
            ->all();

        DB::beginTransaction();
        $product->delete();
        $this->productTypeService->relationService->imageService->deleteImages($images);
        Storage::deleteDirectory('/public/product_images/' . $product->id);
        Storage::deleteDirectory('/public/comments/' . $product->id);
        DB::commit();
    }

    public function publish(Product $product): void
    {
        $product->load('optionValues:id')
            ->productTypes()
            ->whereHas('optionValues', function ($q) use ($product) {
                $q->whereIn('optionValues.id', $product->optionValues);
            })
            ->where('count', '!=', 0)
            ->update(['is_published' => request()->has('publish')]);
    }
}
