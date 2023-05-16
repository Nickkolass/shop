<?php

namespace App\Http\Controllers\Admin\Product;

use App\Models\Product;
use App\Http\Controllers\Admin\Product\DBProductController;
use App\Http\Requests\Product\UpdateRequest;

class ProductUpdateController extends DBProductController
{
    public function __invoke(Product $product, UpdateRequest $request)
    {
        $this->authorize('update', $product);

        $this->service->update($product, session()->pull('edit'), $request->validated());

        return redirect()->route('product.show_product', compact('product'));
    }

}
