<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Product\FilterRequest;
use App\Http\Resources\CartResource;
use App\Http\Resources\Product\DataResource;
use App\Http\Resources\Product\ProductTypeResource;
use App\Models\Category;
use App\Models\ProductType;
use App\Services\API\Back\BackProductService;
use App\Services\API\Back\BackService;

class BackController extends Controller
{
    private $service;
    private $productService;


    public function __construct(BackService $service, BackProductService $productService)
    {
        $this->service = $service;
        $this->productService = $productService;
    }


    public function index()
    {
        $productTypes = $this->service->viewed(request('viewed'));
        return ProductTypeResource::collection($productTypes)->resolve();
    }


    public function products(Category $category, FilterRequest $request)
    {
        $data = $request->validated();
        $this->productService->getData($data, $category);

        return DataResource::make($data)->resolve();
    }


    public function product($category, ProductType $productType)
    {
        $this->service->product($productType);
        return ProductTypeResource::make($productType)->resolve();
    }


    public function cart()
    {
        $productTypes = $this->service->cart(request('cart'));
        return CartResource::collection($productTypes)->resolve();
    }
}
