<?php

namespace App\Http\Controllers;

use App\Models\Traits\HasVerify;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

class HomeController extends Controller
{
    use HasVerify;

    /** Show the application dashboard */
    public function __invoke(): RedirectResponse
    {
        $role = session('user.role');
        $this->verify($role);
        if (!$role) abort(redirect('login'));
        return redirect()->route(($role == User::ROLE_SALER || $role == User::ROLE_ADMIN) ? 'admin.index' : 'client.products.index');
    }
}
