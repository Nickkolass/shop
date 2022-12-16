<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Product\BaseController as ProductBaseController;
use App\Http\Requests\Product\ProductStoreRequest;

class ProductStoreController extends ProductBaseController
{
    public function __invoke(ProductStoreRequest $request)
    {
        $data = $request->validated();

        $this->service->store($data);

        return redirect()->route('product.index_product');
    }
}
