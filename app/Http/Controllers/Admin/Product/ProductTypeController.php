<?php

namespace App\Http\Controllers\Admin\Product;

use App\Dto\Admin\Product\ProductTypeDto;
use App\Dto\Admin\Product\ProductTypeRelationDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Product\ProductType\ProductTypeStoreRequest;
use App\Http\Requests\Admin\Product\ProductType\ProductTypeUpdateRequest;
use App\Models\Product;
use App\Models\ProductType;
use App\Services\Admin\Product\ProductTypeService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ProductTypeController extends Controller
{

    public function __construct(public readonly ProductTypeService $productTypeService)
    {
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Product $product
     * @return View|Factory
     */
    public function create(Product $product): View|Factory|RedirectResponse
    {
        $this->authorize('update', $product);
        $optionValues = $this->productTypeService->relationService->optionValueService->getOptionValues($product);
        if ($optionValues->count() == 0 || $product->productTypes()->count() >= collect([1])->crossjoin(...$optionValues)->count()) {
            abort(400, 'У выбранного товара имеется максимальное количество типов в соответствии с выбранными классификаторами');
        }
        return view('admin.product.productType.create', compact('optionValues', 'product'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ProductTypeStoreRequest $request
     * @param Product $product
     * @return RedirectResponse
     */
    public function store(ProductTypeStoreRequest $request, Product $product): RedirectResponse
    {
        $this->authorize('update', $product);

        $data = $request->validated();
        $data['productTypeRelationDto'] = new ProductTypeRelationDto(...array_pop($data));

        $this->productTypeService->store($product, new ProductTypeDto(...$data));
        return redirect()->route('admin.products.show', $product->id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param ProductType $productType
     * @return View|Factory
     */
    public function edit(ProductType $productType): View|Factory
    {
        $this->authorize('update', $productType);
        $productType->load(['productImages:productType_id,file_path', 'optionValues:id', 'product:id']);
        $optionValues = $this->productTypeService->relationService->optionValueService->getOptionValues($productType->product);
        return view('admin.product.productType.edit', compact('productType', 'optionValues'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ProductTypeUpdateRequest $request
     * @param ProductType $productType
     * @return RedirectResponse
     */
    public function update(ProductTypeUpdateRequest $request, ProductType $productType): RedirectResponse
    {
        $this->authorize('update', $productType);

        $data = $request->validated();
        $data['productTypeRelationDto'] = new ProductTypeRelationDto(...array_pop($data));

        $this->productTypeService->update($productType, new ProductTypeDto(...$data));
        return redirect()->route('admin.products.show', $productType->product_id);
    }

    /**
     * Publish the specified resource in storage.
     *
     * @param ProductType $productType
     * @return RedirectResponse
     */
    public function publish(ProductType $productType): RedirectResponse
    {
        if ($productType->product()->pluck('saler_id')[0] != auth()->id() & session('user.role') != 'admin') abort(403);
        $productType->update(['is_published' => !$productType->is_published]);
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param ProductType $productType
     * @return RedirectResponse
     */
    public function destroy(ProductType $productType): RedirectResponse
    {
        $this->authorize('delete', $productType);
        $this->productTypeService->delete($productType);
        return redirect()->route('admin.products.show', $productType->product_id);
    }
}
