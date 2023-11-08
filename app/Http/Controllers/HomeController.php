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
        $role = $this->verify();
        if (!$role) abort(redirect()->route('login'));
        return redirect()->route(in_array($role, [User::ROLE_SALER, User::ROLE_ADMIN]) ? 'admin.index' : 'client.products.index');
    }
}
