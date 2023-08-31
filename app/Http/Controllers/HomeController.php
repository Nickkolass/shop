<?php

namespace App\Http\Controllers;

use App\Models\Traits\HasVerify;
use Illuminate\Http\RedirectResponse;

class HomeController extends Controller
{
    use HasVerify;

     /** Show the application dashboard */
    public function __invoke(): RedirectResponse
    {
        $role = session('user.role');
        $this->verify($role);
        if(!$role) abort(redirect('login'));
        return redirect()->route(($role == 'saler' || $role == 'admin') ? 'admin.index' : 'client.products.index');
    }
}
