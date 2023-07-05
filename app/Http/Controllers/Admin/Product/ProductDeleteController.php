<?php

namespace App\Http\Controllers\Admin\Product;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;

class ProductDeleteController extends DBProductController
{
    public function __invoke(Product $product): RedirectResponse
    {
        $this->authorize('delete', $product);
        $this->service->delete($product);
        return redirect()->route('admin.products.index');
    }
}
