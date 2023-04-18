<?php

namespace App\Http\Controllers\Admin\Product;

use App\Components\Method;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Group;
use App\Models\Option;
use App\Models\Tag;

class ProductEditController extends Controller
{
    public function __invoke(Product $product)
    {
        $this->authorize('update', $product);
        $product->load(['tags:id', 'productImages:product_id,file_path', 'optionValues:id', 'propertyValues:id,property_id,value', 'category.properties:id,title']);
        
        Method::valuesToKeys($product, 'propertyValues', true);

        $optionValues = Option::with('optionValues:id,option_id,value')->select('id', 'title')->get()->mapWithKeys(function ($option) {
            return [$option->title => $option->optionValues];
        })->toArray();

        $groups = auth()->user()->groups()->select('id', 'title')->get()->toArray();
        $tags = Tag::select(['id', 'title'])->get()->toArray();
        
        $product = $product->toArray();
        $product['option_values'] = array_column($product['option_values'], 'id');
        
        $product['tags'] = array_column($product['tags'], 'id');
        $product['product_images'] = array_column($product['product_images'], 'file_path');

        return view('admin.product.edit_product', compact('tags', 'groups', 'product', 'optionValues'));
    }
}
