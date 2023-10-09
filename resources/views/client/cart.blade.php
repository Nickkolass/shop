@extends('client.layouts.main')
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

        @if(empty($product_types))
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
                    @foreach($product_types as $k => $product_type)
                        <h4 hidden>{{(($product_type['amount'] <= $product_type['count']) & $product_type['is_published']) ?: $block[$product_type['id']]=true}}</h4>
                        <tr style="text-align: center">
                            <td style="vertical-align: middle">{{ $i++ }}</td>
                            <td style="vertical-align: middle">{{ $product_type['title'] }}</td>
                            <td style="vertical-align: middle"><a
                                    href="{{ route('client.products.show', $product_type['id']) }}">
                                    <img
                                        src="{{Storage::url($product_type['preview_image'])}}"
                                        style="opacity:{{$product_type['is_published'] == 0 || $product_type['count'] == 0 || ($block[$product_type['id']] ?? false) ? '0.3' : '1'}}; height: 150px"></a>
                            </td>
                            <td style="vertical-align: middle">
                                @foreach($product_type['option_values'] as $option => $value)
                                    {{$option . ': ' . $value}}<br>
                                @endforeach
                            </td>
                            <td style="vertical-align: middle">{{ $product_type['price'] }} руб.</td>
                            <td style="vertical-align: middle">
                                @include('client.product.components.qty')
                            </td>
                            <td style="vertical-align: middle">{{$product_type['total_price']}} руб.</td>
                            <td style="vertical-align: middle">
                                <form
                                    action="{{route('client.addToCart', ['addToCart' => [$product_type['id'] => '']]) }}"
                                    method="post">
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
                <h4>Итого {{$i-1}} товаров общей стоимостью {{ $total_price }} рублей</h4><br>
                @if(session()->has('user'))
                    <form action="{{route('client.orders.create')}}" method="post">
                        @csrf
                        <input type="hidden" name="total_price" value="{{$total_price}}">
                        <input type="submit" class="btn btn-primary btn-lg" style="height: 50px; width: 200px"
                               @disabled($block ?? false)
                               value="{{($block ?? false) ? 'Имеются недоступные товары' : 'Перейти к оформлению' }}">
                    </form>
                @else
                    <a type="submit" class="btn btn-primary btn-lg" style="height: 50px; width: 200px"
                       href="{{route('login')}}">
                        <h4 style="padding: 10px; margin-left:-15px; text-align: center">Зарегистрируйтесь или
                            войдите</h4></a>
                @endif
            </div>
        @endif
    </main>
@endsection
