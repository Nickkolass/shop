<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryDeleteController extends Controller
{
    public function __invoke (Category $category) {
        $category->delete();
        return redirect()->route('category.index_category');
    }
}
