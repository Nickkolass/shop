<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Admin\Product\DBProductController;
use App\Http\Requests\Product\StoreRequest;
use App\Models\Product;

class ProductStoreController extends DBProductController
{
        
    public function __invoke(StoreRequest $request)
    {
        $this->authorize('create', Product::class);

        $this->service->store(session()->pull('create'), $request->validated()['types']);
        
        return redirect()->route('admin.products.index');
    }
}
