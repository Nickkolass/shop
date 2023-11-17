<?php

namespace App\Components\Payment\src\Http\Controllers;

use App\Components\Payment\src\Services\PaymentService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\UserCardRequest;
use App\Models\Order;
use App\Models\OrderPerformer;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class PaymentController extends Controller
{

    public function __construct(public readonly PaymentService $paymentService)
    {
    }

    public function cardEdit(User $user): View
    {
        $this->authorize('card', $user);
        $widget = $this->paymentService->paymentClient->getWidget();
        return view('payment::card', compact('user', 'widget'));
    }

    public function cardUpdate(UserCardRequest $request, User $user): RedirectResponse
    {
        $this->authorize('card', $user);
        $card = $request->validated()['card'];
        $user->update(['card' => $card]);
        return redirect()->route('users.show', $user->id);
    }

    /** Запрашивается только покупателями из api */
    public function pay(Order $order): string
    {
        return $this->paymentService->pay($order);
    }

    /** Запрашивается только покупателями из api */
    public function refund(Order $order): void
    {
        $this->paymentService->refund($order);
    }

    /** Запрашивается только продавцами из админки */
    public function payout(OrderPerformer $order): RedirectResponse
    {
        $this->paymentService->payout($order);
        return back();
    }

    /** Уведомления от платежной системы */
    public function callback(): Response
    {
        $this->paymentService->paymentClient->authorizeCallback();
        $this->paymentService->callback();
        return response(status: 200)->send();
    }
}
