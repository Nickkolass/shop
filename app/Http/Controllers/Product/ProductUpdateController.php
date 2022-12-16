<?php

namespace App\Http\Controllers\Product;

use App\Http\Requests\Product\ProductUpdateRequest;
use App\Models\Product;
use App\Http\Controllers\Product\BaseController as ProductBaseController;

class ProductUpdateController extends ProductBaseController
{
    public function __invoke (ProductUpdateRequest $request, Product $product) {
       
        $data = $request->validated();
            
        $this->service->update($data, $product);
        
        return redirect()->route('product.show_product', compact('product'));

    }
}
