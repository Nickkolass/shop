<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\UserPasswordRequest;
use App\Http\Requests\Admin\User\UserStoreRequest;
use App\Http\Requests\Admin\User\UserUpdateRequest;
use App\Models\User;
use App\Services\Admin\UserService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class UserController extends Controller
{

    public UserService $service;
    public function __construct(UserService $service)
    {
        $this->authorizeResource(User::class, 'user');
        $this->service = $service;
        $this->middleware('client')->only(['edit', 'show', 'update', 'destroy', 'support']);
        $this->middleware('admin')->only('index', 'create', 'store');
    }

    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index(): View
    {
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
     * @param UserStoreRequest $request
     * @return View
     */
    public function store(UserStoreRequest $request): View
    {
        $data = $request->validated();
        $this->service->store($data);
        return $this->index();
    }

    /**
     * Display the specified resource.
     *
     * @param User $user
     * @return View
     */
    public function show(User $user): View
    {
        return view('user.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param User $user
     * @return View
     */
    public function edit(User $user): View
    {
        return view('user.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UserUpdateRequest $request
     * @param User $user
     * @return View
     */
    public function update(UserUpdateRequest $request, User $user): View
    {
        $data = $request->validated();
        $user->update($data);
        return $this->show($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     * @return RedirectResponse
     */
    public function destroy(User $user): RedirectResponse
    {
        $user->delete();
        return redirect()->route('users.index');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UserPasswordRequest $request
     * @param User $user
     * @return RedirectResponse
     */
    public function password(UserPasswordRequest $request, User $user): RedirectResponse
    {
        $this->authorize('password', $user);
        $data = $request->validated();
        $this->service->password($user, $data['new_password']);
        return redirect()->route('users.show', $user->id);
    }

}
