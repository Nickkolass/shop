<?php

namespace App\Http\Controllers\Admin;

use App\Dto\Admin\UserDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\UserPasswordRequest;
use App\Http\Requests\Admin\User\UserStoreRequest;
use App\Http\Requests\Admin\User\UserUpdateRequest;
use App\Models\User;
use App\Services\Admin\UserService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class UserController extends Controller
{

    public UserService $service;

    public function __construct(UserService $service)
    {
        $this->middleware('client')->only(['edit', 'show', 'update', 'destroy', 'passwordEdit', 'passwordUpdate']);
        $this->middleware('admin')->only(['index', 'create', 'store']);
        $this->authorizeResource(User::class, 'user');
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return View|Factory
     */
    public function index(): View|Factory
    {
        $users = User::query()->toBase()->simplePaginate(5);
        return view('admin.user.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View|Factory
     */
    public function create(): View|Factory
    {
        return view('admin.user.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param UserStoreRequest $request
     * @return View|Factory
     */
    public function store(UserStoreRequest $request): View|Factory
    {
        $data = $request->validated();
        $this->service->store(new UserDto(...$data));
        return $this->index();
    }

    /**
     * Display the specified resource.
     *
     * @param User $user
     * @return View|Factory
     */
    public function show(User $user): View|Factory
    {
        return view('admin.user.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param User $user
     * @return View|Factory
     */
    public function edit(User $user): View|Factory
    {
        return view('admin.user.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UserUpdateRequest $request
     * @param User $user
     * @return View|Factory
     */
    public function update(UserUpdateRequest $request, User $user): View|Factory
    {
        $data = $request->validated();
        $this->service->update($user, new UserDto(...$data));
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
     * Edit the specified resource in storage.
     *
     * @param int $user_id
     * @return View|Factory
     */
    public function passwordEdit(int $user_id): View|Factory
    {
        if ($user_id != auth()->id()) abort(401);
        return view('admin.user.password');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UserPasswordRequest $request
     * @param User $user
     * @return RedirectResponse
     */
    public function passwordUpdate(UserPasswordRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();
        $this->service->passwordUpdate($user, $data['new_password']);
        return redirect()->route('users.show', $user->id);
    }
}
