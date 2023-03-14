<?php

namespace App\Http\Controllers\Admin\Product;

use App\Models\Product;
use App\Http\Requests\Product\ProductUpdateRequest;
use App\Http\Controllers\Admin\Product\DBProductController;

class ProductUpdateController extends DBProductController
{
    public function __invoke (ProductUpdateRequest $request, Product $product) {

        $this->authorize('update', $product);
        $data = $request->validated();
        $this->service->update($data, $product);
        
        return redirect()->route('product.show_product', compact('product'));

    }
}
