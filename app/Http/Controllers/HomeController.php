<?php

namespace App\Http\Controllers;

use App\Models\Traits\HasVerify;
use Illuminate\Http\RedirectResponse;

class HomeController extends Controller
{
    use HasVerify;

     /**
     * Show the application dashboard.
     *
     * @return RedirectResponse
     */
    public function __invoke(): RedirectResponse
    {
        $role = session('user.role');
        $this->verify($role);
        return redirect()->route(($role == 'saler' || $role == 'admin') ? 'admin.index' : 'api.index');
    }
}
