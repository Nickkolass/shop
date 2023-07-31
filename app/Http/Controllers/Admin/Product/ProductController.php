<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Product\StoreRequest;
use App\Http\Requests\Admin\Product\UpdateRequest;
use App\Models\Product;
use App\Services\Admin\Product\ProductService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ProductController extends Controller
{

    public ProductService $service;

    public function __construct(ProductService $service)
    {
        $this->authorizeResource(Product::class, 'product');
        $this->service = $service;
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
     * @param StoreRequest $request
     * @return RedirectResponse
     */
    public function store(StoreRequest $request): RedirectResponse
    {
        $data = $request->validated()['types'];
        $product_id = $this->service->store(session()->pull('create'), $data);
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
     * @param UpdateRequest $request
     * @param Product $product
     * @return RedirectResponse
     */
    public function update(UpdateRequest $request, Product $product): RedirectResponse
    {
        $data = $request->validated();
        $this->service->update($product, session()->pull('edit'), $data);
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
        $this->authorize('update', $product);
        $this->service->publish($product);
        return back();
    }
}
