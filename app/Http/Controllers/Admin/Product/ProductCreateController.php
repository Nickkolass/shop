<?php

namespace App\Http\Controllers\Admin\Product;

use App\Components\Method;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Product\ProductRequest;
use App\Models\Category;
use App\Models\Option;
use App\Models\OptionValue;
use App\Models\Product;
use App\Models\Property;
use App\Models\Tag;
use Illuminate\Contracts\View\View;

class ProductCreateController extends Controller
{
    public function index(): View
    {
        $this->authorize('create', Product::class);
        $tags = Tag::pluck('title', 'id');
        $categories = Category::pluck('title_rus', 'id');
        return view('admin.product.create.index_create', compact('tags', 'categories'));
    }


    public function properties(ProductRequest $request): View
    {

        $data = $request->validated();
        session(['create' => $data]);

        $properties = Property::with('propertyValues:id,property_id,value')->whereHas('categories', function ($q) use ($data) {
            $q->where('category_id', $data['category_id']);
        })->select('id', 'title')->get();

        $optionValues = Option::with('optionValues:id,option_id,value')->select('id', 'title')->get();
        $optionValues = Method::OVPs($optionValues);

        return view('admin.product.create.properties_create', compact('properties', 'optionValues'));
    }


    public function types(): View
    {
        //в случае ошибки валидации редирект на пост роут невозможен, поэтому гет с проверкой внутри метода
        if(app('router')->getRoutes()->match(app('request')->create(url()->previous()))->getName() == 'admin.products.store') abort(404);

        session(['create.propertyValues' => array_filter(request('propertyValues'))]);

        $optionValues = array_merge(...array_values(request('optionValues')));
        $optionValues = OptionValue::with('option:id,title')->select('id', 'option_id', 'value')->find($optionValues);
        $optionValues = Method::toGroups($optionValues);

        return view('admin.product.create.types_create', compact('optionValues'));
    }
}
