<?php

namespace App\Services\Admin\Product;

use App\Models\Category;
use App\Models\Option;
use App\Models\OptionValue;
use App\Models\Property;
use App\Models\Tag;
use App\Services\Methods\Maper;
use Illuminate\Support\Collection;

class ProductCreateEditService
{

    public function index(): array
    {
        $data['tags'] = Tag::pluck('title', 'id');
        $data['categories'] = Category::pluck('title_rus', 'id');
        return $data;
    }

    public function properties(int $category_id): array
    {
        $data['optionValues'] = Option::with('optionValues:id,option_id,value')->select('id', 'title')->get();
        $data['optionValues'] = Maper::OptionOrPropertyValues($data['optionValues']);
        $data['properties'] = Property::query()
            ->whereHas('categories', function ($q) use ($category_id) {
                $q->where('category_id', $category_id);
            })
            ->with('propertyValues:id,property_id,value')
            ->select('id', 'title')
            ->get();
        return $data;
    }

    public function types(array $optionValues): Collection
    {
        $optionValues = array_merge(...array_values($optionValues));
        $optionValues = OptionValue::with('option:id,title')->select('id', 'option_id', 'value')->find($optionValues);
        return Maper::toGroups($optionValues);
    }
}
