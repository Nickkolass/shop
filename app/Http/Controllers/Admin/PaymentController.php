<?php

namespace App\Http\Controllers\Admin;

use App\Dto\PaymentDto;
use App\Enum\PaymentEventEnum;
use App\Http\Controllers\Controller;
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
        $widget = $this->paymentService->getCardWidget($user);
        return view('admin.user.card', compact('widget'));
    }

    public function cardUpdate(User $user): RedirectResponse
    {
        $this->authorize('card', $user);
        $updated = $this->paymentService->cardUpdate($user, request()->input('data'));
        return $updated === true
            ? redirect()->route('users.show', $user->id)
            : back()->withErrors($updated);
    }

    public function payout(OrderPerformer $order): RedirectResponse
    {
        $this->authorize('payout', $order);
        $paymentDto = new PaymentDto(
            payment_type: PaymentEventEnum::PAYMENT_EVENT_PAYOUT,
            order_id: $order->id,
            price: $order->total_price,
            payout_token: auth()->user()->card['payout_token'],
        );
        $this->paymentService->payout($paymentDto);
        return back();
    }
}
