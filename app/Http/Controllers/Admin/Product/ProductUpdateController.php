<?php

namespace App\Http\Controllers\Admin\Product;

use App\Models\Product;
use App\Http\Requests\Product\UpdateRequest;
use Illuminate\Http\RedirectResponse;

class ProductUpdateController extends DBProductController
{
    public function __invoke(Product $product, UpdateRequest $request): RedirectResponse
    {
        $this->authorize('update', $product);

        $this->service->update($product, session()->pull('edit'), $request->validated());

        return redirect()->route('admin.products.show', compact('product'));
    }

}
