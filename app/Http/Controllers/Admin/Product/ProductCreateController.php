<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Product\ProductRelationsRequest;
use App\Http\Requests\Admin\Product\ProductRequest;
use App\Models\Product;
use App\Services\Admin\Product\ProductCreateEditService;
use Illuminate\Contracts\View\View;

class ProductCreateController extends Controller
{

    public function __construct(private readonly ProductCreateEditService $service)
    {
    }

    public function index(): View
    {
        $this->authorize('create', Product::class);
        $data = $this->service->index();
        return view('admin.product.create.index', compact('data'));
    }

    public function relations(ProductRequest $request): View
    {
        $data = $request->validated();
        session(['create.product' => $data]);
        $data = $this->service->relations($data['category_id']);
        return view('admin.product.create.relations', compact('data'));
    }

    public function types(ProductRelationsRequest $request): View
    {
        $data = $request->validated();
        session(['create.relations' => $data]);
        $optionValues = $this->service->types($data['optionValues']);
        return view('admin.product.create.types', compact('optionValues'));
    }
}
