<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Category\CategoryStoreRequest;
use App\Http\Requests\Admin\Category\CategoryUpdateRequest;
use App\Models\Category;
use Illuminate\Contracts\View\View;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return View
     */
    public function index(): View
    {
        $categories = Category::all();
        return view('admin.category.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     * @return View
     */
    public function create(): View
    {
        return view('admin.category.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return View
     */
    public function store(CategoryStoreRequest $request): View
    {
        $data = $request->validated();
        Category::firstOrCreate($data);
        return $this->index();
    }

    /**
     * Display the specified resource.
     *
     * @param  Category $category
     * @return View
     */
    public function show(Category $category): View
    {
        return view('admin.category.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Category $category
     * @return View
     */
    public function edit(Category $category): View
    {
        return view('admin.category.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Category $category
     * @return View
     */
    public function update(CategoryUpdateRequest $request, Category $category): View
    {
        $data = $request->validated();
        $category->update($data);
        return view('admin.category.show', compact('category'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Category $category
     * @return View
     */
    public function destroy(Category $category): View
    {
        $category->delete();
        return $this->index();
    }
}
