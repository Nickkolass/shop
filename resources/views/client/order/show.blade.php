@php use App\Models\Order;use App\Models\OrderPerformer;use App\Models\User; @endphp
@extends('client.layouts.main')
@section('content')

    <div class="page-heading header-orders header-text">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="text-content">
                        <h4>{{'Заказ № '.$order['id'].' от '.$order['created_at']}}</h4>
                        <h2>LUMOS</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <main class="cd-main-content">
        <div class="card-body table-responsive">
            <table class="table table-striped">
                <tbody>
                <tr>
                    <td>Заказ</td>
                    <td>{{ $order['id'] }}</td>
                </tr>

                <tr>
                    <td>Дата заказа</td>
                    <td>{{ $order['created_at'] }}</td>
                </tr>
                <tr>
                    <td>Срок доставки</td>
                    <td>{{ $order['dispatch_time'] }}</td>
                </tr>
                <tr>
                    <td>Статус</td>
                    <td>{{ Order::getStatuses()[$order['status']] }}</td>
                </tr>
                <tr>
                    <td>Способ доставки, получатель</td>
                    <td>{{ $order['delivery'] }}</td>
                </tr>
                <tr>
                    <td>Стоимость заказа</td>
                    <td>{{ $order['total_price'] }}</td>
                </tr>
                <tr>
                    <td style="vertical-align: middle">Товары</td>
                    <td>
                        <table class="table">
                            <tbody>
                            <tr class="table-success" style="text-align: center">
                                <td>Наряд</td>
                                <td>Продавец</td>
                                <td>Стоимость</td>
                                <td>Срок доставки</td>
                                <td>Статус</td>
                                <td>Отказ</td>
                            </tr>
                            @foreach($order['order_performers'] as $order_performer)
                                <tr class="table-info" style="text-align: center; vertical-align: middle">
                                    @can('role', [User::class, User::ROLE_ADMIN])
                                        <td style="vertical-align: middle"><a
                                                href="{{ route('admin.orders.show', $order_performer['id']) }}">
                                                {{ $order_performer['id'] }}</a>
                                        </td>
                                        <td style="vertical-align: middle"><a class="linkclass disabled"
                                                                              href="{{ route('users.show', $order_performer['saler_id']) }}"> {{ $order_performer['saler_name'] }} </a>
                                        </td>
                                    @else
                                        <td style="vertical-align: middle">{{ $order_performer['id'] }}</td>
                                        <td style="vertical-align: middle">{{ $product_type['saler_name'] }}</td>
                                    @endcan
                                    <td style="vertical-align: middle">{{ $order_performer['total_price'] }}</td>
                                    <td style="vertical-align: middle">{{ $order_performer['dispatch_time'] }}</td>
                                    <td style="vertical-align: middle">{{ OrderPerformer::getStatuses()[$order_performer['status']] }}</td>
                                    <td style="vertical-align: middle">
                                        <form
                                            action="{{route('client.orders.destroyOrderPerformer', $order_performer['id']) }}"
                                            method="post">
                                            @csrf
                                            @method('delete')
                                            <input type="submit" class="btn btn-danger btn-lg" value="Отказ"
                                                @disabled($order_performer['status'] != OrderPerformer::STATUS_WAIT_DELIVERY)>
                                        </form>
                                    </td>
                                </tr>
                                @foreach($order_performer['product_types'] as $product_type)
                                    <tr>
                                        <td colspan="2">
                                            <a href="{{ route('client.products.show', $product_type['id']) }}">
                                                <img
                                                    src="{{Storage::url($product_type['preview_image'])}}"
                                                    style="height: 90px"></a></td>
                                        <td colspan="2">
                                            Название: {{ $product_type['title'] }}<br>
                                            Количество: {{ $product_type['amount'] }}<br>
                                            Cтоимость: {{ $product_type['price'] }}<br>
                                        </td>
                                        <td colspan="2">
                                            @foreach($product_type['option_values'] as $option => $value)
                                                {{$option . ': ' . $value}}<br>
                                            @endforeach
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                            </tbody>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="card-header d-flex p-3" style="text-align:center">
            @if($order['status'] == Order::STATUS_PAID)
                <div class="mr-3">
                    <form action="{{ route('client.orders.update', $order['id']) }}" method="post">
                        @csrf
                        @method('patch')
                        <div class="mr-3">
                            <input type="submit" class="btn btn-primary btn-lg" value="Подтвердить получение">
                        </div>
                    </form>
                </div>
                @if($order['cancelable'])
                    <form action="{{route('client.orders.destroy', $order['id']) }}" method="post">
                        @csrf
                        @method('delete')
                        <input type="submit" class="btn btn-danger btn-lg" value="Отказаться">
                    </form>
                @endif
            @elseif($order['status'] == Order::STATUS_WAIT_PAYMENT)
                <div class="mr-3">
                    <form action="{{ route('client.orders.pay', $order['id']) }}" method="post">
                        @csrf
                        <input type="submit" class="btn btn-primary btn-lg" value="Оплатить">
                    </form>
                </div>
                <form action="{{route('client.orders.destroy', $order['id']) }}" method="post">
                    @csrf
                    @method('delete')
                    <input type="hidden" name="due_to_pay" value="true">
                    <input type="submit" class="btn btn-danger btn-lg" value="Отказаться">
                </form>
            @elseif($order['refundable'])
                <div class="mr-3">
                    <form action="{{ route('client.orders.refund', $order['id']) }}" method="post">
                        @csrf
                        <input type="submit" class="btn btn-primary btn-lg" value="Запросить возврат">
                    </form>
                </div>
            @endif
        </div>
    </main>
@endsection
