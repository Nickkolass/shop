<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Payment\PayoutRequest;
use App\Models\OrderPerformer;
use App\Models\User;
use App\Services\Admin\PaymentService;
use Illuminate\Contracts\View\View;
use Symfony\Component\HttpFoundation\RedirectResponse;

class PaymentController extends Controller
{

    public function __construct(public readonly PaymentService $paymentService)
    {
    }

    public function cardEdit(User $user): View
    {
        $this->authorize('card', $user);
        $widget = $this->paymentService->getWidget($user);
        return view('admin.user.card', compact('widget'));
    }

    public function cardUpdate(User $user): RedirectResponse
    {
        $this->authorize('card', $user);
        $data = request()->input('data');
        $is_valid = $this->paymentService->cardValidate(['data' => $data]);
        if($is_valid){
            $this->paymentService->cardUpdate($user, $data);
            return redirect()->route('users.show', $user->id);
        }
        return back();
    }

    public function payout(OrderPerformer $order, PayoutRequest $request): RedirectResponse
    {
        $this->authorize('payout', $order);
        $data = $request->validated();
        $this->paymentService->payout($data);
        return back();
    }
}
