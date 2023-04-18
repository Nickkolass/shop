@extends('api.layouts.main')
@section('content')

<div class="page-heading cart header-text">
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
                    <td>Номер заказа</td>
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
                    <td>{{ $order['status'] }}</td>
                </tr>
                <tr>
                    <td>Способ доставки, получатель</td>
                    <td>{{ $order['delivery'] }}</td>
                </tr>
                <tr>
                    <td style="vertical-align: middle">Товары</td>
                    <td>
                        <table class="table table-striped">
                            <tbody>
                                @foreach($order['products'] as $product)
                                <tr>
                                    <td><a href="{{ route('api.product_api', [$product['category'], $product['id']]) }}">
                                            <img src="{{asset('/storage/'.$product['preview_image'])}}" style="height: 90px"></a></td>
                                    <td>Название: {{ $product['title'] }}<br>
                                        Количество: {{ $product['amount'] }}<br>
                                        Стоимость: {{ $product['price'] }}<br>
                                        Статус: {{ $product['status'] }}<br>
                                        Продавец:
                                        @if(auth()->user()->role == 'admin')
                                        <a class="linkclass disabled" href="{{ route('user.show_user', $product['saler_id']) }}"> {{ $product['saler'] }} </a><br>
                                        <a href="{{ route('order.show_order', $product['orderPerformer_id']) }}">Перейти к заказу</a>
                                        @else
                                        {{ $product['saler'] }}
                                        @endif
                                    </td>
                                    <td>
                                        @foreach($product['optionValues'] as $option => $value)
                                        {{$option . ': ' . $value}}<br>
                                        @endforeach
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>Стоимость заказа</td>
                    <td>{{ $order['total_price'] }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="card-header d-flex p-3" style="text-align:center">
        <div class="mr-3">
            <a href="{{ route('api.support_api') }}" class="btn btn-primary btn-lg">Поддержка</a>
        </div>
        <form action="{{ route('api.orderStatus_api', $order['id']) }}" method="post">
            @csrf
            @method('patch')
            <div class="mr-3">
                <input type="submit" class="btn btn-primary btn-lg" value="Подтвердить получение" @disabled($order['status'] !='В работе' || str_contains('Отправлен', $order['status']))>
            </div>
        </form>
        <form action="{{route('api.orderDelete_api', $order['id']) }}" method="post">
            @csrf
            @method('delete')
            <input type="submit" class="btn btn-danger btn-lg" value="Отказаться" @disabled($order['status'] !='В работе' || str_contains('Отправлен', $order['status']) || !empty(array_diff(array_column($order['products'], 'status' ), ['В работе'])))>
        </form>
    </div>
</main>
@endsection