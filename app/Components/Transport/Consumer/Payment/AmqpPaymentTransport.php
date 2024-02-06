<?php

namespace App\Components\Transport\Consumer\Payment;

use App\Components\Transport\Consumer\AmqpConsumerTransportInterface;
use App\Dto\PaymentDto;
use App\Http\Controllers\Client\API\APIPaymentCallbackController;
use App\Http\Requests\Client\Payment\APICallbackPaymentRequest;
use Log;
use Throwable;

class AmqpPaymentTransport extends AbstractPaymentTransport implements AmqpConsumerTransportInterface
{

    public function payout(PaymentDto $paymentDto): void
    {
        $this->getAmqpTransport()
            ->setExchange(config('consumers.payment.options.amqp.exchange'))
            ->setRoutingKey(config('consumers.payment.options.amqp.routing_key'))
            ->setMessage((string)json_encode($paymentDto))
            ->setReplyTo(config('consumers.payment.requester_id'))
            ->publish();
    }

    public function refund(PaymentDto $paymentDto): void
    {
        $this->getAmqpTransport()
            ->setExchange(config('consumers.payment.options.amqp.exchange'))
            ->setRoutingKey(config('consumers.payment.options.amqp.routing_key'))
            ->setMessage((string)json_encode($paymentDto))
            ->setReplyTo(config('consumers.payment.requester_id'))
            ->publish();
    }

    public static function callback(mixed $message): void
    {
        // если входные данные не проходят валидацию то ошибка выбрасывается на этапе создания реквеста
        // Call to a member function getUrlGenerator() on null
        try {
            $request = new APICallbackPaymentRequest(json_decode($message->body, true));
            $request->setContainer(app())->validateResolved();
            app(APIPaymentCallbackController::class)->callback($request);
        } catch (Throwable $throwable) {
            $message = $throwable->getMessage();
            echo $message . PHP_EOL;
            Log::error($message);
        }
    }
}
