<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\CategoryUpdateRequest;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryUpdateController extends Controller
{
    public function __invoke (CategoryUpdateRequest $request, Category $category) {
        $data = $request->validated();
        $category->update($data);

        return view('category.show_category', compact('category'));   

    }
}
