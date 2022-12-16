<?php

namespace App\Http\Controllers\Group;

use App\Http\Controllers\Controller;
use App\Http\Requests\Group\GroupUpdateRequest;
use App\Models\Group;
use Illuminate\Http\Request;

class GroupUpdateController extends Controller
{
    public function __invoke (GroupUpdateRequest $request, Group $group) {
        $data = $request->validated();
        $group->update($data);

        return view('group.show_group', compact('group'));   

    }
}
