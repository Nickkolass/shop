<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Product\ProductRequest;
use App\Models\Product;
use App\Services\Admin\Product\ProductCreateEditService;
use Illuminate\Contracts\View\View;

class ProductCreateController extends Controller
{
    public ProductCreateEditService $service;

    public function __construct(ProductCreateEditService $service)
    {
        $this->service = $service;
    }
    public function index(): View
    {
        $this->authorize('create', Product::class);
        $data = $this->service->index();
        return view('admin.product.create.index_create', compact('data'));
    }


    public function properties(ProductRequest $request): View
    {
        $data = $request->validated();
        session(['create' => $data]);
        $data = $this->service->properties($data['category_id']);
        return view('admin.product.create.properties_create', compact('data'));
    }


    public function types(): View
    { //в случае ошибки валидации редирект на пост роут невозможен, поэтому гет с проверкой внутри метода: выполняется если выполнится хотя бы 1
        if (url()->previous() != route('admin.products.createProperties') & !session()->pull('validator_failed')) abort(404);
        session(['create.propertyValues' => array_filter(request('propertyValues'))]);
        $optionValues = $this->service->types(request('optionValues'));
        return view('admin.product.create.types_create', compact('optionValues'));
    }
}
