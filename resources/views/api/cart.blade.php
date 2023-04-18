@extends('api.layouts.main')
@section('content')

<div class="page-heading cart header-text">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="text-content">
                    <h4>Корзина</h4>
                    <h2>LUMOS</h2>
                </div>
            </div>
        </div>
    </div>
</div>
<h4 hidden>{{$i = 1, $totalPrice = 0}}</h4>
<main class="cd-main-content">
    <div class="card-body table-responsive">
        <table class="table table-striped">
            <thead>
                <tr style="text-align: center">
                    <th>№ п/п</th>
                    <th>Наименование</th>
                    <th>Изображение</th>
                    <th>Параметры</th>
                    <th>Цена за шт.</th>
                    <th>Количество</th>
                    <th>Сумма</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                <tr style="text-align: center">
                    <td style="vertical-align: middle">{{ $i++ }}</td>
                    <td style="vertical-align: middle">{{ $product['title'] }}</td>
                    <td style="vertical-align: middle"><a href="{{ route('api.product_api', [$product['category'], $product['id']]) }}">
                            <img src="{{asset('/storage/'.$product['preview_image'])}}" style="height: 150px"></a></td>
                    <td style="vertical-align: middle">
                        @foreach($product['optionValues'] as $optionValue)
                        {{$optionValue['option']['title'] . ': ' . $optionValue['value']}}<br>
                        @endforeach
                    </td>
                    <td style="vertical-align: middle">{{ $product['price'] }} руб.</td>
                    <td style="vertical-align: middle">
                    @include('api.components.qty')
                    </td>
                    <td style="vertical-align: middle">{{$product['amount']*$product['price']}} руб.</td>
                </tr>
                <h4 hidden>{{ $totalPrice += $product['amount']*$product['price'] }}</h4>
                @endforeach
            </tbody>
        </table>
    </div>
    @if(!empty($products))
    <form action="{{ route('api.preOrdering_api') }}" style="text-align: center;">
        <h4 style="vertical-align: middle">Итого {{$i-1}} товаров общей стоимостью {{ $totalPrice }} рублей</h4>
        <br>
        @foreach($products as $product)
        <input type="number" name="cart[{{$product['id']}}]" value="{{ $product['amount'] }}" hidden>
        @endforeach
        <input type="number" name="total_price" value="{{ $totalPrice }}" hidden>
        @if (auth()->check())
        <input type="submit" class="btn btn-primary btn-lg" style="height: 50px; width: 200px" value="Перейти к оформлению">
        @else
        <a type="submit" class="btn btn-primary btn-lg" style="height: 50px; width: 200px" href="{{ route('login') }}">Для оформления заказа <br> зарегистрируйтесь или войдите</a>
        @endif
    </form>
    @endif
</main>
@endsection