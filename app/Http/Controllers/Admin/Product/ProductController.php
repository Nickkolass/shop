<?php

namespace App\Http\Controllers\Admin\Product;

use App\Dto\Admin\Product\ProductDto;
use App\Dto\Admin\Product\ProductRelationDto;
use App\Dto\Admin\Product\ProductTypeDto;
use App\Dto\Admin\Product\ProductTypeRelationDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Product\ProductTypesRequest;
use App\Http\Requests\Admin\Product\UpdateRelationsRequest;
use App\Models\Product;
use App\Services\Admin\Product\ProductService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ProductController extends Controller
{

    public function __construct(private readonly ProductService $service)
    {
        $this->authorizeResource(Product::class, 'product');
    }

    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index(): View
    {
        $products = $this->service->index();
        return view('admin.product.index', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ProductTypesRequest $request
     * @return RedirectResponse
     */
    public function store(ProductTypesRequest $request): RedirectResponse
    {
        $productDto = new ProductDto(...session()->pull('create.product'));
        $productRelationDto = new ProductRelationDto(...session()->pull('create.relations'));

        $collectionProductTypeDto = collect($request->validated()['types'])->map(function (array $productType) {
            $productType['productTypeRelationDto'] = new ProductTypeRelationDto(...array_pop($productType));
            return new ProductTypeDto(...$productType);
        });

        $product_id = $this->service->store($productDto, $productRelationDto, $collectionProductTypeDto);

        return redirect()->route('admin.products.show', $product_id);
    }

    /**
     * Display the specified resource.
     *
     * @param Product $product
     * @return View
     */
    public function show(Product $product): View
    {
        $this->service->show($product);
        return view('admin.product.show', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRelationsRequest $request
     * @param Product $product
     * @return RedirectResponse
     */
    public function update(UpdateRelationsRequest $request, Product $product): RedirectResponse
    {
        $this->service->update(
            $product,
            new ProductDto(...session()->pull('edit')),
            new ProductRelationDto(...$request->validated())
        );
        return redirect()->route('admin.products.show', $product->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Product $product
     * @return View
     */
    public function destroy(Product $product): View
    {
        $this->service->delete($product);
        return $this->index();
    }

    /**
     * Publish the specified resource in storage.
     *
     * @param Product $product
     * @return RedirectResponse
     */
    public function publish(Product $product): RedirectResponse
    {
        if ($product->saler_id != auth()->id() && session('user.role') != 'admin') abort(403);
        $this->service->publish($product);
        return back();
    }
}
