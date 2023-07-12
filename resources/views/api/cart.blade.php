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
<h4 hidden>{{$i = 1}}</h4>
<main class="cd-main-content">

    @if(empty($productTypes))
    <br>
    <h4 style="text-align: center">Корзина пока пуста</h4>
    @else
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
                    <th>Удалить</th>
                </tr>
            </thead>
            <tbody>
                @foreach($productTypes as $k => $productType)
                <h4 hidden>{{$productType['amount'] < $productType['count'] ?: $block[$productType['id']]=true}}</h4>
                        <tr style="text-align: center">
                            <td style="vertical-align: middle">{{ $i++ }}</td>
                            <td style="vertical-align: middle">{{ $productType['title'] }}</td>
                            <td style="vertical-align: middle"><a
                                    href="{{ route('api.product', [$productType['category'], $productType['id']]) }}">
                                    <img src="{{asset('/storage/'.$productType['preview_image'])}}"
                                        style="opacity:{{$productType['is_published'] == 0 || $productType['count'] == 0 || ($block[$productType['id']] ?? false) ? '0.3' : '1'}}; height: 150px"></a>
                            </td>
                            <td style="vertical-align: middle">
                                @foreach($productType['option_values'] as $option => $value)
                                {{$option . ': ' . $value}}<br>
                                @endforeach
                            </td>
                            <td style="vertical-align: middle">{{ $productType['price'] }} руб.</td>
                            <td style="vertical-align: middle">
                                @include('api.product.components.qty')
                            </td>
                            <td style="vertical-align: middle">{{$productType['totalPrice']}} руб.</td>
                            <td style="vertical-align: middle">
                            <form action="{{route('api.addToCart', ['addToCart' => [$productType['id'] => 'amount']]) }}" method="post">
                                @csrf
                                <input type="submit" class="btn btn-danger" value="Удалить">
                            </form>
                            </td>
                        </tr>
                        @endforeach
            </tbody>
        </table>
    </div>
    <div style="text-align: center">
        <h4>Итого {{$i-1}} товаров общей стоимостью {{ $totalPrice }} рублей</h4><br>
        <a type="submit" class="btn btn-primary btn-lg" style="height: 50px; width: 200px"
            href="{{ ($block ?? false) ? '#' : (session()->has('user') ? route('api.orders.create', ['totalPrice' => $totalPrice]) : route('login')) }}">
            <h4
                style="padding: 10px;margin-left: {{session()->has('user') ? '0px' : '-15px'}}; text-align: center">
                {{($block ?? false) ? 'Имеются недоступные товары' : (session()->has('user') ? 'Перейти к оформлению' :
                'Зарегистрируйтесь или войдите')}}</h4>
        </a>
    </div>
    @endif
</main>
@endsection
