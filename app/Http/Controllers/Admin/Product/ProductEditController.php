<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Product\ProductRequest;
use App\Models\Product;
use App\Services\Admin\Product\ProductCreateEditService;
use Illuminate\Contracts\View\View;

class ProductEditController extends Controller
{

    public function __construct(private readonly ProductCreateEditService $service)
    {
    }

    public function index(Product $product): View
    {
        $this->authorize('update', $product);
        $product->load('tags:id');
        $data = $this->service->index();
        return view('admin.product.edit.index', compact('product', 'data'));
    }

    public function relations(Product $product, ProductRequest $request): View
    {
        $this->authorize('update', $product);
        $data = $request->validated();
        session(['edit' => $data]);
        $product->setRelations([
            'propertyValues' => $product->propertyValues()->pluck('property_values.id'),
            'optionValues' => $product->optionValues()->pluck('optionValues.id')]);
        $data = $this->service->relations($data['category_id']);
        return view('admin.product.edit.relations', compact('product', 'data'));
    }
}
