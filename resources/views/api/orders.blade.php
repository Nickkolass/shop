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
                    <th>№ п/п</th>
                    <th>Товары</th>
                    <th>Дата заказа</th>
                    <th>Дата доставки</th>
                    <th>Способ доставки, получатель</th>
                    <th>Стоимость заказа</th>
                </tr>
            </thead>
            <h4 hidden>{{$i = 1}}</h4>
            <tbody>
                @foreach($data['orders'] as $order)
                <tr style="text-align: center">
                    <td style="vertical-align: middle">{{ $i++ }}</td>
                    <td style="vertical-align: middle">
                    @foreach($order['products'] as $product)
                    <a href="{{ route('api.show_api', [$product['category'], $product['id']]) }}">
                    <img src="{{asset('/storage/'.$product['preview_image'])}}" style="height: 50px"></a>
                    @endforeach
                    </td>
                    <td style="vertical-align: middle">{{ $order['created_at'] }}</td>
                    <td style="vertical-align: middle">{{ $order['created_at'] }}</td>
                    <td style="vertical-align: middle">{{ $order['delivery'] }}</td>
                    <td style="vertical-align: middle">{{ $order['total_price'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
</main>
@endsection