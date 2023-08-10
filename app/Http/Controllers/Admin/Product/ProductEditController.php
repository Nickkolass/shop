<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Product\ProductRequest;
use App\Models\Product;
use App\Services\Admin\Product\ProductCreateEditService;
use Illuminate\Contracts\View\View;

class ProductEditController extends Controller
{
    public ProductCreateEditService $service;

    public function __construct(ProductCreateEditService $service)
    {
        $this->service = $service;
    }

    public function index(Product $product): View
    {
        $this->authorize('update', $product);
        $product->load('tags:id');
        $data = $this->service->index();
        return view('admin.product.edit.index_edit', compact('product', 'data'));
    }

    public function properties(Product $product, ProductRequest $request): View
    {
        $data = $request->validated();
        session(['edit' => $data]);
        $product->load('propertyValues:id', 'optionValues:id');
        $data = $this->service->properties($data['category_id']);
        return view('admin.product.edit.properties_edit', compact('product', 'data'));
    }
}
