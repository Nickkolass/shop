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
        $role = $this->verify();
        return redirect()->route(($role == 'saler' || $role == 'admin') ? 'admin.index' : 'api.index');
    }
}
