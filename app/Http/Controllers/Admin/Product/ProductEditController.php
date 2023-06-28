<?php

namespace App\Http\Controllers\Admin\Product;

use App\Components\Method;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\Option;
use App\Models\Property;
use App\Models\Tag;

class ProductEditController extends Controller
{
    public function index(Product $product)
    {
        $this->authorize('update', $product);

        $product->tags = $product->tags()->pluck('tags.id');
        $tags = Tag::pluck('title', 'id');
        $categories = Category::pluck('title_rus', 'id');

        return view('admin.product.edit.index_edit', compact('product', 'tags', 'categories'));
    }


    public function properties(Product $product, ProductRequest $request)
    {
        $data = $request->validated();
        session(['edit' => $data]);

        $productPV_ids = $product->propertyValues()->pluck('property_value_id');
        $productOV_ids = $product->optionValues()->pluck('optionValue_id');

        $properties = Property::with('propertyValues:id,property_id,value')->whereHas('categories', function ($q) use ($data) {
            $q->where('category_id', $data['category_id']);
        })->select('id', 'title')->get();

        $optionValues = Option::with('optionValues:id,option_id,value')->select('id', 'title')->get();
        $optionValues = Method::OVPs($optionValues);

        return view('admin.product.edit.properties_edit', compact('product', 'properties', 'optionValues', 'productPV_ids', 'productOV_ids'));
    }

    
    public function publish(Product $product)
    {
        $this->authorize('update', $product);
        $product->productTypes()->update(['is_published' => request()->has('publish') ? 1 : 0]);
        return back();
    }
}
