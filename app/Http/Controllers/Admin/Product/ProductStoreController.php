<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Requests\Admin\Product\StoreRequest;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;

class ProductStoreController extends DBProductController
{

    public function __invoke(StoreRequest $request): RedirectResponse
    {
        $this->authorize('create', Product::class);
        $this->service->store(session()->pull('create'), $request->validated()['types']);
        return redirect()->route('admin.products.index');
    }
}
