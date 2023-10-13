<?php

namespace App\Http\Controllers\Client\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\Product\FilterRequest;
use App\Http\Resources\CartResource;
use App\Http\Resources\IndexResource;
use App\Http\Resources\Product\ProductFilterAggregateResource;
use App\Http\Resources\Product\ProductTypeResource;
use App\Models\Category;
use App\Models\ProductType;
use App\Models\User;
use App\Services\Client\API\Product\ProductCartService;
use App\Services\Client\API\Product\ProductFilterService;
use App\Services\Client\API\Product\ProductShowService;
use App\Services\Client\API\Product\ProductViewedLikedService;

class APIProductController extends Controller
{

    public function __construct(
        public ProductViewedLikedService $productViewedLikedService,
        public ProductFilterService      $productFilterService,
        public ProductShowService        $productShowService,
        public ProductCartService        $productCartService,
    )
    {
    }

    /**
     * @return array<mixed>
     */
    public function index(): array
    {
        if (auth('api')->check()) $data['liked'] = $this->productViewedLikedService->getProductTypes();
        if ($viewed = request('viewed')) $data['viewed'] = $this->productViewedLikedService->getProductTypes($viewed);
        return isset($data) ? IndexResource::make($data)->resolve() : [];
    }

    /**
     * @param Category $category
     * @param FilterRequest $request
     * @return array<mixed>
     */
    public function filter(Category $category, FilterRequest $request): array
    {
        $data = $request->validated();
        $result = $this->productFilterService->getProductFilterAggregateDataCache($data, $category);
        return ProductFilterAggregateResource::make($result)->resolve();
    }

    /**
     * @param ProductType $productType
     * @return array<mixed>
     */
    public function show(ProductType $productType): array
    {
        $this->productShowService->loadRelationsProductType($productType);
        return ProductTypeResource::make($productType)->resolve();
    }

    /**
     * @return array{}|array<mixed>
     */
    public function cart(): array
    {
        $productTypes = $this->productCartService->getProductTypes(request('cart'));
        return $productTypes->count() != 0 ? CartResource::collection($productTypes)->resolve() : [];
    }

    /**
     * @return array{}|array<mixed>
     */
    public function liked(): array
    {
        $this->authorize('product', User::class);
        $productTypes = $this->productViewedLikedService->getProductTypes();
        return $productTypes->count() != 0 ? ProductTypeResource::collection($productTypes)->resolve() : [];
    }
}
