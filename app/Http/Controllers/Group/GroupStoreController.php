<?php

namespace App\Http\Controllers\Group;

use App\Http\Controllers\Controller;
use App\Http\Requests\Group\GroupStoreRequest;
use App\Models\Group;
use Illuminate\Http\Request;

class GroupStoreController extends Controller
{
public function __invoke (GroupStoreRequest $request) {

        $data = $request->validated();
        Group::firstOrCreate($data);
        return redirect()->route('group.index_group');
    }
}
