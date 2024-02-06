<?php

namespace App\Http\Controllers\Client\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\Product\ProductsRequest;
use App\Services\Client\Front\FrontProductService;
use Illuminate\Contracts\View\View;

class FrontProductController extends Controller
{
    public function __construct(private readonly FrontProductService $productService)
    {
    }

    public function index(): View
    {
        $data = $this->productService->index();
        return view('client.index', compact('data'));
    }

    public function filter(string $category_title, ProductsRequest $request): View
    {
        $data = [];
        $query_params = $request->validated();
        $product_types = $this->productService->filter($category_title, $query_params, $data);
        return view('client.product.index', compact('data', 'product_types'));
    }

    public function show(int $product_type_id): View
    {
        $data = [];
        $product_type = $this->productService->show($product_type_id, $data);
        return view('client.product.show', compact('data', 'product_type'));
    }

    public function cart(): View
    {
        $data = $this->productService->cart();
        return view('client.cart', $data);
    }

    public function liked(): View
    {
        $product_types = $this->productService->liked();
        return view('client.liked', compact('product_types'));
    }
}
