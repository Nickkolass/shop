<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Option\OptionStoreRequest;
use App\Http\Requests\Option\OptionUpdateRequest;
use App\Models\Option;
use App\Models\OptionValue;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class OptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index(): View
    {
        $options = Option::all();
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
     * @param  \Illuminate\Http\Request  $request
     * @return View
     */
    public function store(OptionStoreRequest $request): View|string
    {
        $data = $request->validated();

        DB::beginTransaction();
        try {
            $option_id = Option::firstOrCreate(['title' => $data['title']])->id;
            foreach ($data['optionValues'] as &$oV) $oV['option_id'] = $option_id;
            OptionValue::insert($data['optionValues']);
            DB::commit();
            return $this->index();
        } catch (\Exception $exception) {
            DB::rollBack();
            return $exception->getMessage();
        }
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
     * @param  \Illuminate\Http\Request  $request
     * @param  Option $option
     * @return View
     */
    public function update(OptionUpdateRequest $request, Option $option): View|string
    {
        $data = $request->validated();

        $optionValues = $option->optionValues()->pluck('value')->toArray();
        $delete = array_diff($optionValues, array_column($data['optionValues'], 'value'));
        foreach ($data['optionValues'] as $k => &$oV) {
            if (in_array($oV['value'], $optionValues)) unset($data['optionValues'][$k]);
            else ($oV['option_id'] = $option->id);
        }
        DB::beginTransaction();
        try {
            $option->optionValues()->whereIn('value', $delete)->delete();
            OptionValue::insert($data['optionValues']);
            $option->update(['title' => $data['title']]);
            DB::commit();
            return $this->show($option);
        } catch (\Exception $exception) {
            DB::rollBack();
            return $exception->getMessage();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Option $option
     * @return View
     */
    public function destroy(Option $option): View
    {
        $option->delete();
        return $this->index();
    }
}
