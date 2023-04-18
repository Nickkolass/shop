<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Admin\Product\DBProductController;
use App\Http\Requests\Product\ProductStoreRequest;

class ProductStoreController extends DBProductController
{
        
    public function __invoke(ProductStoreRequest $request)
    {
        $data = $request->validated();

        $this->service->store($data);
        
        return redirect()->route('product.index_product');
    }
}
