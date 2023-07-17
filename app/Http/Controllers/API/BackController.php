<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Product\FilterRequest;
use App\Http\Requests\API\RatingAndComment\StoreRequest;
use App\Http\Resources\CartResource;
use App\Http\Resources\IndexResource;
use App\Http\Resources\Product\DataResource;
use App\Http\Resources\Product\ProductTypeResource;
use App\Http\Resources\Product\ShowProductTypeResource;
use App\Models\Category;
use App\Models\ProductType;
use App\Models\User;
use App\Services\API\Back\BackProductsService;
use App\Services\API\Back\BackService;

class BackController extends Controller
{
    private BackService $service;
    private BackProductsService $productsService;


    public function __construct(BackService $service, BackProductsService $productsService)
    {
        $this->service = $service;
        $this->productsService = $productsService;
    }

    public function index(): array
    {
        !auth('api')->check() ?: $data['liked'] = $this->service->getLiked();
        !request()->has('viewed') ?: $data['viewed'] = $this->service->getViewed(request('viewed'));
        return isset($data) ? array_filter(IndexResource::make($data)->resolve()) : [];
    }


    public function products(Category $category, FilterRequest $request): array
    {
        $data = $request->validated();
        $this->productsService->getData($data, $category);
        return DataResource::make($data)->resolve();
    }


    public function product(ProductType $productType): array
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
        $this->authorize('like', User::class);
        $productTypes = $this->service->getLiked();
        return ProductTypeResource::collection($productTypes)->resolve();
    }

    public function likedToggle(ProductType $productType): void
    {
        $this->authorize('like', User::class);
        $productType->liked()->toggle(auth('api')->id());
    }

    public function commentStore(StoreRequest $request): array
    {
        $this->authorize('like', User::class);
        $data = $request->validated();
        $this->service->commentStore($data);
        return $this->product(ProductType::find($request->input('productType_id')));
    }
}
