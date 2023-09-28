@php use Illuminate\Support\Facades\Storage; @endphp
@extends('client.layouts.main')
@section('content')

    <div class="page-heading orders header-text">
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
        @if(empty($orders['data']))
            <h4 style="text-align:center"><br> Надеемся, что скоро здесь появится первый заказ</h4>
        @else
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
                            <td style="vertical-align: middle"><a
                                    href="{{ route('client.orders.show', $order['id']) }}">{{ $order['id'] }}</a></td>
                            <td style="vertical-align: middle">
                                @if ($order['status'] == 'В работе' || str_starts_with($order['status'], 'Отправлен'))
                                    <form action="{{ route('client.orders.update', $order['id']) }}" method="post">
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
                                @foreach($order['product_types'] as $product_type)
                                    <a href="{{ route('client.products.show', $product_type['id']) }}">
                                        <img
                                            src="{{Storage::url($product_type['preview_image'])}}"
                                            style="height: 50px"></a>
                                    {{$product_type['amount'].' шт.'}}
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
            @include('client.product.components.paginate')
        @endif
    </main>
@endsection
