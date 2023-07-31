@extends('api.layouts.main')
@section('content')

    <div class="page-heading orders header-text">
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
                    <td>Стоимость заказа</td>
                    <td>{{ $order['total_price'] }}</td>
                </tr>
                <tr>
                    <td style="vertical-align: middle">Товары</td>
                    <td>
                        <table class="table table-striped">
                            <tbody>
                            @foreach($order['productTypes'] as $productType)
                                <tr>
                                    <td><a href="{{ route('api.product', $productType['id']) }}">
                                            <img src="{{asset('/storage/'.$productType['preview_image'])}}"
                                                 style="height: 90px"></a></td>
                                    <td>Название: {{ $productType['title'] }}<br>
                                        Количество: {{ $productType['amount'] }}<br>
                                        Стоимость: {{ $productType['price'] }}<br>
                                        Статус: {{ $productType['status'] }}<br>
                                        Продавец:
                                        @if(session('user.role') == 'admin')
                                            <a class="linkclass disabled"
                                               href="{{ route('users.show', $productType['saler_id']) }}"> {{ $productType['saler'] }} </a>
                                            <br>
                                            <a href="{{ route('admin.orders.show', $productType['orderPerformer_id']) }}">Перейти
                                                к заказу</a>
                                        @else
                                            {{ $productType['saler'] }}
                                        @endif
                                    </td>
                                    <td>
                                        @foreach($productType['optionValues'] as $option => $value)
                                            {{$option . ': ' . $value}}<br>
                                        @endforeach
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="card-header d-flex p-3" style="text-align:center">
            <form action="{{ route('api.orders.update', $order['id']) }}" method="post">
                @csrf
                @method('patch')
                <div class="mr-3">
                    <h4 hidden> {{$blockCancel = str_starts_with($order['status'], 'Отменен')}} </h4>
                    <h4 hidden> {{$blockReceived = str_starts_with($order['status'], 'Получен')}} </h4>
                    <h4 hidden> {{$blockSent = str_starts_with($order['status'], 'Отправлен')}} </h4>
                    <input type="submit" class="btn btn-primary btn-lg" value="Подтвердить получение"
                           title="{{$blockCancel ? 'Заказ уже отменен' : ($blockReceived ? 'Заказ уже получен': '')}}" @disabled($blockCancel || $blockReceived)>
                </div>
            </form>
            <form action="{{route('api.orders.destroy', $order['id']) }}" method="post">
                @csrf
                @method('delete')
                <input type="submit" class="btn btn-danger btn-lg" value="Отказаться"
                       title="{{$blockCancel ? 'Заказ уже отменен' : ($blockReceived ? 'Заказ уже получен' : ($blockSent ? 'Заказ уже отправлен' : ''))}}" @disabled($order['status'] != 'В работе' || $blockSent || !empty(array_diff(array_column($order['productTypes'], 'status' ), ['В работе'])))>
            </form>
        </div>
    </main>
@endsection
