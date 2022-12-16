<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\CategoryStoreRequest;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryStoreController extends Controller
{
public function __invoke (CategoryStoreRequest $request) {
        $data = $request->validated();
        Category::firstOrCreate($data);
        return redirect()->route('category.index_category');
    }
}
