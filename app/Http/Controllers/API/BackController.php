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
        if(auth('api')->check()) $data['liked'] = $this->service->getLiked();
        if($viewed = request('viewed')) $data['viewed'] = $this->service->getViewed($viewed);
        return IndexResource::make($data ?? [])->resolve();
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


    public function cart(): ?array
    {
        $productTypes = $this->service->cart(request('cart'));
        return isset($productTypes) ? CartResource::collection($productTypes)->resolve() : null;
    }

    public function liked(): ?array
    {
        $this->authorize('product', User::class);
        $productTypes = $this->service->getLiked();
        return isset($productTypes) ? ProductTypeResource::collection($productTypes)->resolve() : null;
    }

    public function likedToggle(ProductType $productType): void
    {
        $this->authorize('product', User::class);
        $productType->liked()->toggle(auth('api')->id());
    }

    public function commentStore(StoreRequest $request): void
    {
        $this->authorize('product', User::class);
        $data = $request->validated();
        $this->service->commentStore($data);
    }
}
