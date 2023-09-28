<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryRequest;
use App\Models\Category;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class CategoryController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(Category::class, 'category');
    }

    /**
     * Display a listing of the resource.
     * @return View|Factory
     */
    public function index(): View|Factory
    {
        $categories = Category::query()->toBase()->get();
        return view('admin.category.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     * @return View|Factory
     */
    public function create(): View|Factory
    {
        return view('admin.category.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CategoryRequest $request
     * @return View|Factory
     */
    public function store(CategoryRequest $request): View|Factory
    {
        $data = $request->validated();
        Category::query()->firstOrCreate($data);
        return $this->index();
    }

    /**
     * Display the specified resource.
     *
     * @param Category $category
     * @return View|Factory
     */
    public function show(Category $category): View|Factory
    {
        return view('admin.category.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Category $category
     * @return View|Factory
     */
    public function edit(Category $category): View|Factory
    {
        return view('admin.category.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param CategoryRequest $request
     * @param Category $category
     * @return View|Factory
     */
    public function update(CategoryRequest $request, Category $category): View|Factory
    {
        $data = $request->validated();
        $category->update($data);
        return $this->show($category);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Category $category
     * @return RedirectResponse
     */
    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();
        return redirect()->route('admin.categories.index');
    }
}
