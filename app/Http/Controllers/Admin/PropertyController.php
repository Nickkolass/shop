<?php

namespace App\Http\Controllers\Admin;

use App\Dto\Admin\PropertyDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PropertyRequest;
use App\Models\Category;
use App\Models\Property;
use App\Services\Admin\PropertyService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class PropertyController extends Controller
{

    public PropertyService $service;

    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index(): View
    {
        $properties = Property::query()->pluck('title', 'id');
        return view('admin.property.index', compact('properties'));
    }

    public function __construct(PropertyService $service)
    {
        $this->authorizeResource(Property::class, 'property');
        $this->service = $service;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create(): View
    {
        $categories = Category::query()->pluck('title_rus', 'id');
        return view('admin.property.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param PropertyRequest $request
     * @return View
     */
    public function store(PropertyRequest $request): View
    {
        $data = $request->validated();
        $this->service->store(new PropertyDto(...$data));
        return $this->index();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Property $property
     * @return View
     */
    public function edit(Property $property): View
    {
        $property->load('propertyValues:property_id,value', 'categories:id,title_rus');
        $categories = Category::query()->pluck('title_rus', 'id');
        return view('admin.property.edit', compact('property', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param PropertyRequest $request
     * @param Property $property
     * @return View
     */
    public function update(PropertyRequest $request, Property $property): View
    {
        $data = $request->validated();
        $this->service->update($property, new PropertyDto(...$data));
        return $this->show($property);
    }

    /**
     * Display the specified resource.
     *
     * @param Property $property
     * @return View
     */
    public function show(Property $property): View
    {
        $property->load('propertyValues:property_id,value', 'categories:title_rus');
        return view('admin.property.show', compact('property'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Property $property
     * @return RedirectResponse
     */
    public function destroy(Property $property): RedirectResponse
    {
        $property->delete();
        return redirect()->route('admin.properties.index');
    }
}
