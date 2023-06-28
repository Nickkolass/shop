<?php

namespace App\Http\Controllers;

use App\Models\Traits\HasVerify;
class HomeController extends Controller
{
    use HasVerify;

     /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function __invoke()
    {
        $role = session('user_role');
        $this->verify($role);
        return redirect()->route(($role == 'saler' || $role == 'admin') ? 'admin.index' : 'api.index');
    }
}
