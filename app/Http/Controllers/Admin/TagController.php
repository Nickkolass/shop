<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Contracts\View\Factory;
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
     * @return View|Factory
     */
    public function index(): View|Factory
    {
        $tags = Tag::query()->toBase()->get();
        return view('admin.tag.index', compact('tags'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View|Factory
     */
    public function create(): View|Factory
    {
        return view('admin.tag.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return View|Factory
     */
    public function store(): View|Factory
    {
        Tag::query()->firstOrCreate(['title' => request('title')]);
        return $this->index();
    }

    /**
     * Display the specified resource.
     *
     * @param Tag $tag
     * @return View|Factory
     */
    public function show(Tag $tag): View|Factory
    {
        return view('admin.tag.show', compact('tag'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Tag $tag
     * @return View|Factory
     */
    public function edit(Tag $tag): View|Factory
    {
        return view('admin.tag.edit', compact('tag'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Tag $tag
     * @return View|Factory
     */
    public function update(Tag $tag): View|Factory
    {
        $tag->update(['title' => request('title')]);
        return $this->show($tag);
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
