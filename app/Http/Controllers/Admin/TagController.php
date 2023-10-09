<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
        $tags = Tag::query()->toBase()->get();
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
     * @return View
     */
    public function store(): View
    {
        Tag::query()->firstOrCreate(['title' => request('title')]);
        return $this->index();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Tag $tag
     * @return View
     */
    public function edit(Tag $tag): View
    {
        return view('admin.tag.edit', compact('tag'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Tag $tag
     * @return View
     */
    public function update(Tag $tag): View
    {
        $tag->update(['title' => request('title')]);
        return $this->show($tag);
    }

    /**
     * Display the specified resource.
     *
     * @param Tag $tag
     * @return View
     */
    public function show(Tag $tag): View
    {
        return view('admin.tag.show', compact('tag'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Tag $tag
     * @return RedirectResponse
     */
    public function destroy(Tag $tag): RedirectResponse
    {
        $tag->products()->detach();
        $tag->delete();
        return redirect()->route('admin.tags.index');
    }
}
