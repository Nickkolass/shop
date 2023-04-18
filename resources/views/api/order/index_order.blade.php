@extends('api.layouts.main')
@section('content')

<div class="page-heading cart header-text">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="text-content">
                    <h4>Заказы</h4>
                    <h2>LUMOS</h2>
                </div>
            </div>
        </div>
    </div>
</div>
<main class="cd-main-content">
    <div class="card-body table-responsive">
        <table class="table table-striped">
            <thead>
                <tr style="text-align: center">
                    <th>Заказ</th>
                    <th>Статус</th>
                    <th>Товары</th>
                    <th>Дата заказа</th>
                    <th>Срок доставки</th>
                    <th>Способ доставки, получатель</th>
                    <th>Стоимость заказа</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders['data'] as $order)
                <tr style="text-align: center">
                    <td style="vertical-align: middle"><a href="{{ route('api.orderShow_api', $order['id']) }}">{{ $order['id'] }}</a></td>
                    <td style="vertical-align: middle">
                        @if ($order['status'] == 'В работе' || str_contains('Отправлен', $order['status']))
                        <form action="{{ route('api.orderStatus_api', $order['id']) }}" method="post">
                            @csrf
                            @method('patch')
                            <div class="form-group">
                                <input type="submit" class="btn-btn-primary" value="Подтвердить получение">
                            </div>
                        </form>
                        @else
                        {{$order['status']}}
                        @endif
                    </td>
                    <td style="vertical-align: middle">
                        @foreach($order['products'] as $product)
                        <a href="{{ route('api.product_api', [$product['category'], $product['id']]) }}">
                            <img src="{{asset('/storage/'.$product['preview_image'])}}" style="height: 50px"></a>
                        {{$product['amount'].' шт.'}}
                        @endforeach
                    </td>
                    <td style="vertical-align: middle">{{ $order['created_at'] }}</td>
                    <td style="vertical-align: middle">{{ $order['dispatch_time'] }}</td>
                    <td style="vertical-align: middle">{{ $order['delivery'] }}</td>
                    <td style="vertical-align: middle">{{ $order['total_price'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if(empty($orders['data']))
    <h4 style="text-align:center">Надеемся, что скоро здесь появится первый заказ</h4>
    @else
    @include('api.components.paginate')
    @endif
</main>
@endsection