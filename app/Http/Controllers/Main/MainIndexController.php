<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;

class MainIndexController extends Controller
{
    public function __invoke (){
        
          $user_id = Auth::user()->id;
          $x_xsrf_token = Cookie::get('X-XSRF-TOKEN');
          DB::table('personal_access_tokens')->where('tokenable_id', '=', $user_id)->update(['token' => $x_xsrf_token]);
          return response(redirect()->route('dashboard'))->cookie('X-XSRF-TOKEN', $x_xsrf_token, 60);

        return view('main.index_main');
    }
}
