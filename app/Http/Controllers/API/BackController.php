<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\IndexRequest;
use App\Http\Requests\API\Product\FilterRequest;
use App\Http\Requests\API\RatingAndComment\StoreRequest;
use App\Http\Resources\CartResource;
use App\Http\Resources\IndexResource;
use App\Http\Resources\Product\DataResource;
use App\Http\Resources\Product\ProductTypeResource;
use App\Http\Resources\Product\ShowProductTypeResource;
use App\Models\Category;
use App\Models\CommentImage;
use App\Models\ProductType;
use App\Models\RatingAndComment;
use App\Services\API\Back\BackProductsService;
use App\Services\API\Back\BackService;
use Illuminate\Http\UploadedFile;

class BackController extends Controller
{
    private BackService $service;
    private BackProductsService $productsService;


    public function __construct(BackService $service, BackProductsService $productsService)
    {
        $this->service = $service;
        $this->productsService = $productsService;
    }

    public function index(IndexRequest $request): array
    {
        $data = $request->validated();
        empty($data['user_id']) ?: $data['liked'] = $this->service->getLiked($data['user_id']);
        empty($data['viewed']) ?: $data['viewed'] = $this->service->getViewed($data['viewed']);
        return array_filter(IndexResource::make($data)->resolve());
    }


    public function products(Category $category, FilterRequest $request): array
    {
        $data = $request->validated();
        $this->productsService->getData($data, $category);
        return DataResource::make($data)->resolve();
    }


    public function product(string $category_title, ProductType $productType): array
    {
        $this->service->product($productType);
        return ShowProductTypeResource::make($productType)->resolve();
    }


    public function cart(): array
    {
        $productTypes = $this->service->cart(request('cart'));
        return CartResource::collection($productTypes)->resolve();
    }

    public function liked(): array
    {
        $productTypes = $this->service->getLiked(request('user_id'));
        return ProductTypeResource::collection($productTypes)->resolve();
    }

    public function likedToggle(ProductType $productType): void
    {
        $productType->liked()->toggle(request('user_id'));
    }

    public function commentStore(StoreRequest $request): void
    {
        $data = $request->validated();

        if (empty($data['commentImages'])) RatingAndComment::create($data);
        else {
            foreach ($data['commentImages'] as $img) $commentImages[] = new UploadedFile(...$img);
            unset($data['commentImages']);
            $comment_id = RatingAndComment::create($data)->id;
            $this->service->commentImages($commentImages, $data['product_id'], $comment_id);
            CommentImage::insert($commentImages);
        }
    }
}
