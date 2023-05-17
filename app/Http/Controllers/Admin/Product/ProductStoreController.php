<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Admin\Product\DBProductController;
use App\Http\Requests\Product\StoreRequest;

class ProductStoreController extends DBProductController
{
        
    public function __invoke(StoreRequest $request)
    {
        $types = $request->validated()['types'];

        $this->service->store(session()->pull('create'), $types);
        
        return redirect()->route('admin.products.index');
    }
}
