<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Tag\TagStoreRequest;
use App\Http\Requests\Admin\Tag\TagUpdateRequest;
use App\Models\Tag;
use Illuminate\Contracts\View\View;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index(): View
    {
        $tags = Tag::all();
        return view('admin.tag.index', compact('tags'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create(): View
    {
        return view('admin.tag.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return View
     */
    public function store(TagStoreRequest $request): View
    {
        $data = $request->validated();
        Tag::firstOrCreate($data);
        return $this->index();
    }

    /**
     * Display the specified resource.
     *
     * @param  Tag $tag
     * @return View
     */
    public function show(Tag $tag): View
    {
        return view('admin.tag.show', compact('tag'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Tag $tag
     * @return View
     */
    public function edit(Tag $tag): View
    {
        return view('admin.tag.edit', compact('tag'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Tag $tag
     * @return View
     */
    public function update(TagUpdateRequest $request, Tag $tag): View
    {
        $data = $request->validated();
        $tag->update($data);
        return view('admin.tag.show', compact('tag'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Tag $tag
     * @return View
     */
    public function destroy(Tag $tag): View
    {
        $tag->products()->detach();
        $tag->delete();
        return $this->index();
    }
}
