<?php

namespace App\Http\Controllers\Admin\Product;

use App\Components\Method;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductType\ProductTypeStoreRequest;
use App\Http\Requests\ProductType\ProductTypeUpdateRequest;
use App\Models\Option;
use App\Models\Product;
use App\Models\ProductType;
use App\Services\Product\ProductTypeService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ProductTypeController extends Controller
{

    public $productTypeService;

    public function __construct(ProductTypeService $productTypeService)
    {
        $this->productTypeService = $productTypeService;
    }


    /**
     * Show the form for creating a new resource.
     *
     * @param  Product $product
     * @return View
     */
    public function create(Product $product): View
    {
        $this->authorize('update', $product);

        $optionValues = Option::select('id', 'title')->with('optionValues:id,option_id,value')->whereHas('optionValues', function ($q) use ($product) {
            $q->whereHas('products', function ($b) use ($product) {
                $b->where('product_id', $product->id);
            });
        })->get();
        $optionValues = Method::OVPs($optionValues);

        return view('admin.product.productType.create', compact('optionValues', 'product'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Product $product
     * @return RedirectResponse
     */
    public function store(ProductTypeStoreRequest $request, Product $product): RedirectResponse
    {
        $this->authorize('update', $product);
        $data = $request->validated();
        $this->productTypeService->store($product, $data, false);
        return redirect()->route('admin.products.show', $product->id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  ProductType $productType
     * @return View
     */
    public function edit(ProductType $productType): View
    {
        $this->authorize('update', $productType);

        $productType->load(['productImages:productType_id,file_path', 'optionValues:id']);
        $optionValues = Option::select('id', 'title')->with('optionValues:id,option_id,value')->whereHas('optionValues', function ($q) use ($productType) {
            $q->whereHas('products', function ($b) use ($productType) {
                $b->where('product_id', $productType->product_id);
            });
        })->get();
        $optionValues = Method::OVPs($optionValues);

        return view('admin.product.productType.edit', compact('productType', 'optionValues'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  ProductType $productType
     * @return RedirectResponse
     */
    public function update(ProductTypeUpdateRequest $request, ProductType $productType): RedirectResponse
    {
        $this->authorize('update', $productType);
        $data = $request->validated();
        $this->productTypeService->update($productType, $data);
        return redirect()->route('admin.products.show', $productType->product_id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  ProductType $productType
     * @return RedirectResponse
     */
    public function publish(ProductType $productType): RedirectResponse
    {
        $this->authorize('update', $productType);
        $productType->update(['is_published' => $productType->is_published == 0 ? 1 : 0]);
        return back();
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  ProductType $productType
     * @return RedirectResponse
     */
    public function destroy(ProductType $productType): RedirectResponse
    {
        $this->authorize('delete', $productType);
        $this->productTypeService->delete($productType);
        return redirect()->route('admin.products.show', $productType->product_id);
    }
}
