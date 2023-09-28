<?php

namespace App\Http\Controllers\Admin;

use App\Dto\Admin\PropertyDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PropertyRequest;
use App\Models\Category;
use App\Models\Property;
use App\Services\Admin\PropertyService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class PropertyController extends Controller
{

    public PropertyService $service;

    public function __construct(PropertyService $service)
    {
        $this->authorizeResource(Property::class, 'property');
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return View|Factory
     */
    public function index(): View|Factory
    {
        $properties = Property::query()->pluck('title', 'id');
        return view('admin.property.index', compact('properties'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View|Factory
     */
    public function create(): View|Factory
    {
        $categories = Category::query()->pluck('title_rus', 'id');
        return view('admin.property.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param PropertyRequest $request
     * @return View|Factory
     */
    public function store(PropertyRequest $request): View|Factory
    {
        $data = $request->validated();
        $this->service->store(new PropertyDto(...$data));
        return $this->index();
    }

    /**
     * Display the specified resource.
     *
     * @param Property $property
     * @return View|Factory
     */
    public function show(Property $property): View|Factory
    {
        $property->load('propertyValues:property_id,value', 'categories:title_rus');
        return view('admin.property.show', compact('property'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Property $property
     * @return View|Factory
     */
    public function edit(Property $property): View|Factory
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
     * @return View|Factory
     */
    public function update(PropertyRequest $request, Property $property): View|Factory
    {
        $data = $request->validated();
        $this->service->update($property, new PropertyDto(...$data));
        return $this->show($property);
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
