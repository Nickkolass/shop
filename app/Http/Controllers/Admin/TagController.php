<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Tag\TagStoreRequest;
use App\Http\Requests\Admin\Tag\TagUpdateRequest;
use App\Models\Tag;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class TagController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(Tag::class, 'tag');
    }

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
     * @param  TagStoreRequest  $request
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
     * @param  TagUpdateRequest  $request
     * @param  Tag $tag
     * @return RedirectResponse
     */
    public function update(TagUpdateRequest $request, Tag $tag): RedirectResponse
    {
        $data = $request->validated();
        $tag->update($data);
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Tag $tag
     * @return RedirectResponse
     */
    public function destroy(Tag $tag): RedirectResponse
    {
        $tag->products()->detach();
        $tag->delete();
        return redirect()->route('admin.tags.index');
    }
}
