<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\UserStoreRequest;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Arhitector\Yandex\Disk;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Show the application registration form.
     *
     * @return View
     */
    public function showRegistrationForm()
    {
        $policy = (new Disk(config('services.yandexdisk.oauth_token')))->getResource('Policy.txt')->get('docviewer');
        return view('auth.register', compact('policy'));
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     * @return User
     */

    protected function create(array $data)
    {
        $data['password'] = Hash::make($data['password']);
        return User::firstOrCreate(['email' => $data['email']], $data);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, (new UserStoreRequest)->rules());
    }

}
