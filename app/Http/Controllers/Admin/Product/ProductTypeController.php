<?php

namespace App\Http\Controllers\Admin\Product;

use App\Components\Method;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductType\ProductTypeCreateRequest;
use App\Http\Requests\ProductType\ProductTypeUpdateRequest;
use App\Models\Option;
use App\Models\Product;
use App\Models\ProductType;
use App\Services\Product\ProductTypeService;

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
     * @return \Illuminate\Http\Response
     */
    public function create(Product $product)
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
     * @return \Illuminate\Http\Response
     */
    public function store(ProductTypeCreateRequest $request, Product $product)
    {
        $this->authorize('update', $product);
        $data = $request->validated();
        $this->productTypeService->storeType($product, $data, false);
        return redirect()->route('admin.products.show', $product->id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ProductType  $productType
     * @return \Illuminate\Http\Response
     */
    public function edit(ProductType $productType)
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
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ProductType  $productType
     * @return \Illuminate\Http\Response
     */
    public function update(ProductTypeUpdateRequest $request, ProductType $productType)
    {
        $this->authorize('update', $productType);
        $data = $request->validated();
        $this->productTypeService->updateType($productType, $data);
        return redirect()->route('admin.products.show', $productType->product_id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\ProductType  $productType
     * @return \Illuminate\Http\Response
     */
    public function published(ProductType $productType)
    {
        $this->authorize('update', $productType);
        $productType->update(['is_published' => $productType->is_published == 0 ? 1 : 0]);
        return back();
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ProductType  $productType
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProductType $productType)
    {
        $this->authorize('delete', $productType);
        $this->productTypeService->deleteType($productType);
        return redirect()->route('admin.products.show', $productType->product_id);
    }
}
