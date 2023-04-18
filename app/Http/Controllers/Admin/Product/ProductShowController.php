<?php

namespace App\Http\Controllers\Admin\Product;

use App\Components\Method;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\APIBackService;

class ProductShowController extends Controller
{
    public function __invoke(Product $product)
    {
        $this->authorize('view', $product);
        $product->load([
            'productImages:product_id,file_path', 'group:id,title', 'category:id,title,title_rus',
            'propertyValues.property:id,title', 'optionValues.option:id,title', 'tags:id,title', 
        ]);

        $product->setRelation('productImages', $product->productImages->pluck('file_path'));
        Method::optionsAndProperties($product);

        return view('admin.product.show_product', compact('product'));
    }
}
