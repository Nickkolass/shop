<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Option\OptionStoreRequest;
use App\Http\Requests\Option\OptionUpdateRequest;
use App\Models\Option;
use App\Models\OptionValue;

class OptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $options = Option::all();
        return view('admin.option.index_option', compact('options'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.option.create_option');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(OptionStoreRequest $request)
    {
        $data = $request->validated();
        $option = Option::firstOrCreate($data['title']);
        foreach($data['optionValues'] as $optionValue){
            OptionValue::firstOrCreate(['option_id' => $option->id, 'value' => $optionValue]);
        }
        return $this->index();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Option $option)
    {
        $option->load('optionValues:option_id,value');
        return view('admin.option.show_option', compact('option'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Option $option)
    {
        $option->load('optionValues:option_id,value');
        return view('admin.option.edit_option', compact('option'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(OptionUpdateRequest $request, Option $option)
    {
        $data = $request->validated();
        $data['optionValues'] = array_filter($data['optionValues']);
        $optionValues = $option->optionValues()->pluck('value')->toArray();
        
        array_map(function($optionValue) use ($option) {
            OptionValue::firstOrCreate(['option_id' => $option->id, 'value' => $optionValue]);
        }, array_diff ($data['optionValues'], $optionValues));

        $option->optionValues()->whereIn('value', array_diff ($optionValues, $data['optionValues']))->delete();
        $option->update(['title' => $data['title']]);
        
        return $this->show($option);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Option $option)
    {
        $option->delete();
        return $this->index();
    }
}
