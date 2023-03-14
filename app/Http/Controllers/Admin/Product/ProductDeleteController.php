<?php

namespace App\Http\Controllers\Admin\Product;

use App\Models\Product;
use App\Http\Controllers\Admin\Product\DBProductController;

class ProductDeleteController extends DBProductController
{
    public function __invoke (Product $product) {
        
        $this->authorize('delete', $product);
        $this->service->delete($product);

        return redirect()->route('product.index_product');
    }
}
