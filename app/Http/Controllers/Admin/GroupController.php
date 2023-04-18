<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Group\GroupStoreRequest;
use App\Http\Requests\Group\GroupUpdateRequest;
use App\Models\Category;
use App\Models\Group;
use App\Models\Product;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        auth()->user()->role == 'admin' ? $groups = Group::query() :  $groups = auth()->user()->groups();
        $groups = $groups->with(['category:id,title_rus', 'products:id,group_id,preview_image'])->simplePaginate(8);
        
        return view('admin.group.index_group', compact('groups'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        auth()->user()->role == 'admin' ? $products = Product::query() : $products = auth()->user()->products();
        $products = $products->select(['id', 'title'])->get();
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
     * @param  \App\Models\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function show(Group $group)
    {
        $this->authorize('view', $group);
        $group->load(['products:id,group_id,title,preview_image', 'category:id,title_rus']);
        return view('admin.group.show_group', compact('group'));   
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function edit(Group $group)
    {
        $this->authorize('update', $group);
        auth()->user()->role == 'admin' ? $products = Product::query() : $products = auth()->user()->products();
        $products = $products->select(['id', 'title', 'group_id'])->get();
        $group->load(['category:id,title,title_rus']);
        $categories = Category::all();
        return view('admin.group.edit_group', compact('group', 'categories', 'products'));   
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function update(GroupUpdateRequest $request, Group $group)
    {
        $this->authorize('update', $group);
        $data = $request->validated();
        $data['saler_id'] = auth()->id();
        
        if(!empty($data['products'])){
            $group->products()->update(['group_id'=>null]);
            Product::whereIn('id', $data['products'])->update(['group_id'=>$group->id]);
            unset($data['products']);
        }
        $group->update($data);
        return $this->show($group);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function destroy(Group $group)
    {
        $this->authorize('delete', $group);
        !request('all') ? $group->products()->update(['group_id'=>null]) : '';
        $group->delete();
        return $this->index();
    }
}
