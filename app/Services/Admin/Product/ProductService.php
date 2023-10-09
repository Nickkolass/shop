<?php

namespace App\Services\Admin\Product;

use App\Dto\Admin\Product\ProductDto;
use App\Dto\Admin\Product\ProductRelationDto;
use App\Dto\Admin\Product\ProductTypeDto;
use App\Exceptions\Admin\ProductException;
use App\Models\Product;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ProductService
{

    public function __construct(public readonly ProductTypeService $productTypeService)
    {
    }

    public function index(): Paginator
    {
        $user = session('user');
        return Product::query()
            ->when($user['role'] != 'admin', function (Builder $q) use ($user) {
                $q->whereHas('saler', fn(Builder $q) => $q->where('id', $user['id']));
            })
            ->select('id', 'title', 'saler_id', 'category_id', 'rating', 'count_rating', 'count_comments')
            ->latest()
            ->with([
                'category:id,title_rus',
                'productTypes:id,product_id,preview_image',
            ])
            ->simplePaginate(4);
    }

    /**
     * @param ProductDto $productDto
     * @param ProductRelationDto $productRelationDto
     * @param Collection<ProductTypeDto> $collectionProductTypeDto
     * @return int|RedirectResponse
     */
    public function store(
        ProductDto         $productDto,
        ProductRelationDto $productRelationDto,
        Collection         $collectionProductTypeDto,
    ): int|RedirectResponse
    {
        DB::beginTransaction();
        try {
            $product = Product::query()->firstOrCreate(['title' => $productDto->title], (array)$productDto);
            $this->productTypeService->relationService->createRelationsProduct($product, $productRelationDto, true);

            $collectionProductTypeRelationsForInsertDto = collect();
            foreach ($collectionProductTypeDto as $productTypeDto) {
                $collectionProductTypeRelationsForInsertDto->push($this->productTypeService->storeType($product, $productTypeDto, true));
            }
            $this->productTypeService->relationService->createRelationsProductTypes($collectionProductTypeRelationsForInsertDto);
            DB::commit();
            return $product->id;
        } catch (Throwable $e) {
            return ProductException::failedStoreProductOrType($e, $productDto, $productRelationDto, $product->id ?? null);
        }
    }

    public function show(Product $product): void
    {
        $product->load([
            'category:id,title,title_rus',
            'tags:id,title',
            'propertyValues' => function (Builder $q) {
                /** @phpstan-ignore-next-line */
                $q->select('value')->selectParentTitle();
            },
            'optionValues' => function (Builder $q) {
                /** @phpstan-ignore-next-line */
                $q->select('value', 'option_id')->selectParentTitle();
            },
            'ratingAndComments' => function (Builder $q) {
                $q->with([
                    'user:id,name',
                    'commentImages:comment_id,file_path'
                ]);
            },
            'productTypes' => function (Builder $q) {
                $q->select('id', 'product_id', 'count', 'price', 'is_published', 'preview_image', 'count_likes')
                    ->with([
                        'productImages:productType_id,file_path',
                        'optionValues:option_id,value'
                    ]);
            }])
            ->setRelation('optionValues', $product->optionValues->groupBy('option_id'));
    }

    public function update(Product $product, ProductDto $productDto, ProductRelationDto $productRelationDto): void
    {
        DB::beginTransaction();
        $product->update((array)$productDto);
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
        Storage::deleteDirectory('product_images/' . $product->id);
        Storage::deleteDirectory('comment_images/' . $product->id);
        DB::commit();
    }

    public function publish(Product $product): void
    {
        $product->load('optionValues:id')
            ->productTypes()
            ->where('count', '!=', 0)
            ->whereHas('optionValues', fn(Builder $q) => $q->whereIn('optionValues.id', $product->optionValues))
            ->update(['is_published' => request()->has('publish')]);
    }
}
