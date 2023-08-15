<?php

namespace App\Services\Admin\Product;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductType;
use App\Models\ProductTypeOptionValue;
use App\Services\Methods\Maper;
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

        $products->map(fn(Product $product) => Maper::countingRatingAndComments($product));
        return $products;
    }

    public function store(array $data, array $types): int
    {
        $relations = $this->productTypeService->relationService->getRelations($data);
        $relations['optionValues'] = array_filter(array_unique(array_merge(...array_column($types, 'optionValues'))));

        DB::beginTransaction();
        try {
            $product = Product::firstOrCreate(['title' => $data['title']], $data);
            $this->productTypeService->relationService->relationsProduct($product, $relations);
            foreach ($types as $type) $attach[] = $this->productTypeService->storeType($product, $type, true);

            ProductTypeOptionValue::insert(array_merge(...array_column($attach, 'optionValues')));
            $productImages = array_merge(...array_column($attach, 'productImages'));
            ProductImage::insert($productImages);
            DB::commit();
            return $product->id;
        } catch (\Exception $e) {
            if (isset ($productImages)) $file_paths = array_column($productImages, 'file_path');
            if (isset($attach)) $file_paths = collect($attach)->pluck('preview_image')->flatten()->merge($file_paths ?? [])->all();
            if (isset ($file_paths)) $this->productTypeService->imageService->deleteImages($file_paths);
            Storage::deleteDirectory('/public/product_images/' . $product->id);
            Storage::deleteDirectory('/public/preview_images/' . $product->id);
            report($e);
            abort(back()->withErrors([$e->getMessage()])->withInput());
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

        Maper::countingRatingAndComments($product);
        Maper::optionsAndProperties($product);
        $product->productTypes->map(fn(ProductType $productType) => Maper::valuesToKeys($productType, 'optionValues'));
    }

    public function update(Product $product, array $data, array $relations): void
    {
        DB::beginTransaction();
        $product->update($data);
        $this->productTypeService->relationService->relationsProduct($product, $relations, false);
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
        $this->productTypeService->imageService->deleteImages($images);
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
            ->update(['is_published' => request()->has('publish') ? 1 : 0]);
    }
}
