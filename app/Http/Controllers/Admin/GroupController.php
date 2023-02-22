<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Group\GroupStoreRequest;
use App\Http\Requests\Group\GroupUpdateRequest;
use App\Models\Category;
use App\Models\Group;
use App\Models\Product;

class GroupController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (auth()->user()->role == 'admin') {
            $groups = Group::with(['category:id,title_rus', 'products:id,group_id,preview_image'])->paginate(5);
        } else {
            $groups = auth()->user()->groups()->with(['category:id,title_rus', 'products:id,group_id,preview_image'])->paginate(5);
        }
        return view('admin.group.index_group', compact('groups'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $products = auth()->user()->products()->get();
        $categories = Category::all();

        return view('admin.group.create_group', compact('products', 'categories'));   
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(GroupStoreRequest $request)
    {
        $data = $request->validated();
        $data['saler_id'] = auth()->id();

        if(empty($data['products'])){
            Group::firstOrCreate($data);
        } else {
            $products = Product::find($data['products']);
            unset($data['products']);
            $group = Group::firstOrCreate($data);
            $group->products()->saveMany($products);
        }
        return $this->index();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Group $group)
    {
        $group->load(['products:id,group_id,title,preview_image', 'category:id,title_rus']);
        return view('admin.group.show_group', compact('group'));   
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Group $group)
    {
        $group->load(['category:id,title,title_rus']);
        $categories = Category::all();
        $products = auth()->user()->products()->select(['id', 'title', 'group_id'])->get();
        return view('admin.group.edit_group', compact('group', 'categories', 'products'));   
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(GroupUpdateRequest $request, Group $group)
    {
        $data = $request->validated();
        $data['saler_id'] = auth()->id(0);
        if(empty($data['products'])){
            $group->update($data);
        } else {
            $products = Product::find($data['products']);
            
            unset($data['products']);
            $group->update($data);
            $group->products()->update(['group_id'=>null]);
            $group->products()->saveMany($products);
        }
        return $this->show($group);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Group $group)
    {
        $group->products()->update(['group_id'=>null]);
        $group->delete();
        return $this->index();
    }
}
