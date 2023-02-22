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
                    <th>Цена за шт.</th>
                    <th>Количество</th>
                    <th>Сумма</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['products'] as $product)
                <tr style="text-align: center">
                    <td style="vertical-align: middle">{{ $i++ }}</td>
                    <td style="vertical-align: middle">{{ $product['title'] }}</td>
                    <td style="vertical-align: middle"><a href="{{ route('api.show_api', [$product['category'], $product['id']]) }}">
                            <img src="{{asset('/storage/'.$product['preview_image'])}}" style="height: 150px"></a></td>
                    <td style="vertical-align: middle">{{ $product['price'] }} руб.</td>
                    <td style="vertical-align: middle">
                        <div class="qty mt-5">
                            <form action="{{ route('api.addToCart_api') }}" method="post">
                                @csrf
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-default btn-number" data-type="minus" data-field="addToCart[{{$product['id']}}]">
                                        <span class="minus bg-dark">-</span>
                                    </button>
                                </span>
                                <input type="number" name="addToCart[{{$product['id']}}]" class="count" min="0" max="{{$product['count']}}" value="{{ $product['amount'] }}">
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-default btn-number" data-type="plus" data-field="addToCart[{{$product['id']}}]">
                                        <span class="plus bg-dark">+</span>
                                    </button>
                                </span>
                                <br>
                                <input type="submit" class="btn btn-primary btn-lg" style="height: 35px" value="Обновить">
                            </form>
                        </div>
                    </td>
                    <td style="vertical-align: middle">{{$product['amount']*$product['price']}} руб.</td>
                </tr>
                <h4 hidden>{{ $totalPrice = $totalPrice + $product['amount']*$product['price'] }}</h4>
                @endforeach
            </tbody>
        </table>
    </div>
    @if(!empty($data['products']))
    <form action="{{ route('api.order_api') }}" method="post" style="text-align: center;" enctype="multipart/form-data">
        @csrf
        <h4 style="vertical-align: middle">Итого {{$i-1}} товаров общей стоимостью {{ $totalPrice }} рублей</h4>
        <br>
        @foreach($data['products'] as $product)
        <input type="number" name="cart[{{$product['id']}}]" value= "{{ $product['amount'] }}" hidden>
        @endforeach
        <input type="number" name="total_price" value= "{{ $totalPrice }}" hidden>
        <input type="submit" class="btn btn-primary btn-lg" style="height: 50px; width: 200px" value="Перейти к оформлению">
    </form>
    @endif
</main>
@endsection