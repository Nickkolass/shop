<?php

namespace App\Http\Controllers\Tag;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tag\TagStoreRequest;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagStoreController extends Controller
{
public function __invoke (TagStoreRequest $request) {

        $data = $request->validated();
        Tag::firstOrCreate($data);
        return redirect()->route('tag.index_tag');
    }
}
