<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Product\FilterRequest;
use App\Http\Requests\API\Product\ViewedRequest;
use App\Http\Resources\CartResource;
use App\Http\Resources\ProductShowResource;
use App\Http\Resources\ProductsResource;
use App\Http\Resources\ViewedResource;
use App\Models\Category;
use App\Models\Product;
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


    public function index(ViewedRequest $request)
    {
        $data = $request->validated();
        $products = $this->service->viewed($data);
        return ViewedResource::collection($products)->resolve();
    }


    public function products(Category $category, FilterRequest $request)
    {
        $data = $request->validated();
        $this->productService->getData($data, $category);
        return ProductsResource::make($data)->resolve();
    }


    public function product($category, Product $product)
    {
        $this->service->product($product, $_REQUEST);
        return ProductShowResource::make($product)->resolve();
    }


    public function cart()
    {
        $cart = $this->service->cart($_REQUEST);
        return CartResource::collection($cart)->resolve();
    }
}
