<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Product\BaseController as ProductBaseController;
use App\Models\Product;

class ProductDeleteController extends ProductBaseController
{
    public function __invoke (Product $product) {
        
        $this->service->delete($product);

        return redirect()->route('product.index_product');
    }
}
