@extends('api.layouts.main')
@section('content')

<div class="page-heading cart header-text">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="text-content">
                    <h4>{{'Заказ № '.$data['order']['id'].' от '.$data['order']['created_at']}}</h4>
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
                    <td>{{ $data['order']['id'] }}</td>
                </tr>

                <tr>
                    <td>Дата заказа</td>
                    <td>{{ $data['order']['created_at'] }}</td>
                </tr>
                <tr>
                    <td>Срок доставки</td>
                    <td>{{ $data['order']['dispatch_time'] }}</td>
                </tr>
                <tr>
                    <td>Статус</td>
                    @if (empty($data['order']['deleted_at']))
                    <td>{{ $data['order']['status'] == '0000-00-00' ? 'В работе' : 'Получен ' . $data['order']['status'] }}</td>
                    @else
                    <td>{{ 'Отменен ' . $data['order']['deleted_at'] }}</td>
                    @endif
                </tr>
                <tr>
                    <td>Способ доставки, получатель</td>
                    <td>{{ $data['order']['delivery'] }}</td>
                </tr>
                <tr>
                    <td style="vertical-align: middle">Товары</td>
                    <td>
                        <table class="table table-striped">
                            <tbody>
                                @foreach($data['products'] as $product)
                                <tr>
                                    <td><a href="{{ route('api.product_api', [$product['category']['title'], $product['id']]) }}">
                                            <img src="{{asset('/storage/'.$product['preview_image'])}}" style="height: 90px"></a></td>
                                    <td>Название: {{ $product['title'] }}<br>
                                        Количество: {{ $product['amount'] }}<br>
                                        Стоимость: {{ $product['price'] }}<br>
                                        Статус:
                                        @if (empty($product['deleted_at']))
                                        {{ $product['status'] == '0000-00-00' ? 'В работе' : 'Отправлен продавцом ' . $product['status'] }}
                                        @else
                                        {{'Отменен ' . $product['deleted_at'] }}
                                        @endif<br>
                                        Продавец:
                                        @if(auth()->user()->role == 'admin')
                                        <a class="linkclass disabled" href="{{ route('user.show_user', $product['saler']['id']) }}"> {{ $product['saler']['name'] }} </a><br>
                                        <a href="{{ route('order.show_order', $product['orderPerformer_id']) }}">Перейти к заказу</a>
                                        @else
                                        {{ $product['saler']['name'] }}
                                        @endif


                                    </td>
                                </tr>
                                @if ($product['status'] == '0000-00-00')
                                @continue
                                @else
                                <h1 hidden>{{$status = true}}</h1>
                                @endif
                                @endforeach
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>Стоимость заказа</td>
                    <td>{{ $data['order']['total_price'] }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="card-header d-flex p-3" style="text-align:center">
        <div class="mr-3">
            <a href="{{ route('api.support_api') }}" class="btn btn-primary btn-lg">Поддержка</a>
        </div>
        <form action="{{ route('api.orderStatus_api', $data['order']['id']) }}" method="post">
            @csrf
            @method('patch')
            <div class="mr-3">
                <input type="submit" class="btn btn-primary btn-lg" value="Подтвердить получение" {{ $data['order']['status'] != '0000-00-00' ? 'disabled' : '' }} {{ !empty($data['order']['deleted_at']) ? 'disabled' : '' }}>
            </div>
        </form>
        <form action="{{route('api.orderDelete_api', $data['order']['id']) }}" method="post">
            @csrf
            @method('delete')
            <input type="submit" class="btn btn-danger btn-lg" value="Отказаться" {{ !empty($status) ? 'disabled' : '' }} {{ !empty($data['order']['deleted_at']) ? 'disabled' : '' }} {{ $data['order']['status'] != '0000-00-00' ? 'disabled' : '' }}>
        </form>
    </div>
</main>
@endsection