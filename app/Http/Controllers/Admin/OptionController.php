<?php

namespace App\Http\Controllers\Admin;

use App\Dto\Admin\OptionDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\OptionRequest;
use App\Models\Option;
use App\Services\Admin\OptionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class OptionController extends Controller
{

    public OptionService $service;

    public function __construct(OptionService $service)
    {
        $this->authorizeResource(Option::class, 'option');
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index(): View
    {
        $options = Option::pluck('title', 'id');
        return view('admin.option.index', compact('options'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create(): View
    {
        return view('admin.option.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  OptionRequest  $request
     * @return View
     */
    public function store(OptionRequest $request): View
    {
        $data = $request->validated();
        $this->service->store(new OptionDto(...$data));
        return $this->index();
    }

    /**
     * Display the specified resource.
     *
     * @param  Option $option
     * @return View
     */
    public function show(Option $option): View
    {
        $option->load('optionValues:option_id,value');
        return view('admin.option.show', compact('option'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Option $option
     * @return View
     */
    public function edit(Option $option): View
    {
        $option->load('optionValues:option_id,value');
        return view('admin.option.edit', compact('option'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  OptionRequest  $request
     * @param  Option $option
     * @return View
     */
    public function update(OptionRequest $request, Option $option): View
    {
        $data = $request->validated();
        $this->service->update($option, new OptionDto(...$data));
        return $this->show($option);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Option $option
     * @return RedirectResponse
     */
    public function destroy(Option $option): RedirectResponse
    {
        $option->delete();
        return redirect()->route('admin.options.index');
    }
}
