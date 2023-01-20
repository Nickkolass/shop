<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        if(!isset(auth()->user()->role)) {
            $this->middleware('auth');
        }
        else $this->middleware('saler') ;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if(auth()->user()->role !== 'client') {
        return view('main.index_main');
        }
        else return redirect()->route('client.index_client');

    }
}
