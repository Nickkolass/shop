<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserStoreRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Mail\MailRegistered;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('client')->only(['edit', 'show', 'update', 'destroy', 'support']);
        $this->middleware('admin')->only('index');
    }

    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index(): View
    {
        $this->authorize('viewAny', User::class);
        $users = User::simplePaginate(5);
        return view('user.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create(): View
    {
        return view('user.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return View
     */
    public function store(UserStoreRequest $request): View
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $user = User::firstOrCreate([
            'email' => $data['email']
        ], $data);
        event(new Registered($user));
        Mail::to($user->email)->send(new MailRegistered());
        return $this->index();
    }

    /**
     * Display the specified resource.
     *
     * @param  User  $user
     * @return View
     */
    public function show(User $user): View
    {
        $this->authorize('view', $user);
        return view('user.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  User  $user
     * @return View
     */
    public function edit(User $user): View
    {
        $this->authorize('update', $user);
        return view('user.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  User  $user
     * @return View
     */
    public function update(UserUpdateRequest $request, User $user): View
    {
        $this->authorize('update', $user);
        $data = $request->validated();
        $data = array_diff($data, $user->toArray());
        $user->update($data);
        return view('user.show', compact('user'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  User  $user
     * @return View
     */
    public function destroy(User $user): View
    {
        $this->authorize('delete', $user);
        $user->delete();
        return $this->index();
    }

    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function support(): View
    {
        return view('admin.support');
    }
}
