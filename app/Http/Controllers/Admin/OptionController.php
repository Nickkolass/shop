<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Option\CommentStoreRequest;
use App\Http\Requests\Option\CommentUpdateRequest;
use App\Models\Option;
use App\Models\OptionValue;
use Illuminate\Support\Facades\DB;

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
        return view('admin.option.index', compact('options'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.option.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CommentStoreRequest $request)
    {
        $data = $request->validated();

        DB::beginTransaction();
        try {
            $option_id = Option::firstOrCreate(['title' => $data['title']])->id;
            foreach ($data['optionValues'] as &$oV) $oV['option_id'] = $option_id;
            OptionValue::insert($data['optionValues']);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return $exception->getMessage();
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
        return view('admin.option.show', compact('option'));
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
        return view('admin.option.edit', compact('option'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CommentUpdateRequest $request, Option $option)
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
        } catch (\Exception $exception) {
            DB::rollBack();
            return $exception->getMessage();
        }

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
