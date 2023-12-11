<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Payment\PayoutRequest;
use App\Models\OrderPerformer;
use App\Models\User;
use App\Services\Admin\AdminPaymentService;
use Illuminate\Contracts\View\View;
use Symfony\Component\HttpFoundation\RedirectResponse;

class PaymentController extends Controller
{

    public function __construct(public readonly AdminPaymentService $paymentService)
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
        $card = $this->paymentService->cardValidate(['data' => request()->input('data')]);
        $this->paymentService->cardUpdate($user, $card);
        return redirect()->route('users.show', $user->id);
    }

    public function payout(OrderPerformer $order, PayoutRequest $request): RedirectResponse
    {
        $this->authorize('payout', $order);
        $data = $request->validated();
        $this->paymentService->payout($data);
        return back();
    }
}
