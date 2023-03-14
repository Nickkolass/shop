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
                @foreach($data['orders']['data'] as $order)
                <tr style="text-align: center">
                    <td style="vertical-align: middle"><a href="{{ route('api.orderShow_api', $order['id']) }}">{{ $order['id'] }}</a></td>
                    <td style="vertical-align: middle">
                        @if (!empty($order['deleted_at']))
                        {{ 'Отменен ' . $order['deleted_at'] }}
                        @else
                        @if ($order['status'] == '0000-00-00')
                        <form action="{{ route('api.orderStatus_api', $order['id']) }}" method="post">
                            @csrf
                            @method('patch')
                            <div class="form-group">
                                <input type="submit" class="btn-btn-primary" value="Подтвердить получение">
                            </div>
                        </form>
                        @else
                        Получен {{$order['status']}}
                        @endif
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

    @if(empty($data['orders']['data']))
    <h4 style="text-align:center">По вашему запросу заказов не найдено</h4>
    @else
    @if($data['orders']['last_page'] != 1)
    <div>
        <nav aria-label="Page navigation example">
            <ul class="pagination justify-content-center">
                <li class="page-item {{ $data['orders']['current_page'] == 1 ? 'disabled' : ''}}">
                    <a class="page-link" href="{{ $data['orders']['first_page_url'] }}" aria-label="First">
                        <span aria-hidden="true">&laquo;</span>
                        <span class="sr-only">В начало</span>
                    </a>
                </li>
                <li class="page-item {{ $data['orders']['current_page'] == 1 ? 'disabled' : ''}}">
                    <a class="page-link" href="{{ $data['orders']['prev_page_url'] }}" tabindex="-1">Назад</a>
                </li>
                @foreach ($data['orders']['links'] as $link)
                <li class="page-item {{ $link ['active'] == true ? 'active' : ''}}"><a class="page-link" href="{{ $link['url'] }}">{{$link['label']}}</a></li>
                @endforeach
                <li class="page-item {{ $data['orders']['last_page'] == $data['orders']['current_page'] ? 'disabled' : ''}}">
                    <a class="page-link" href="{{ $data['orders']['next_page_url'] }}">Вперед</a>
                </li>
                <li class="page-item {{ $data['orders']['last_page'] == $data['orders']['current_page'] ? 'disabled' : ''}}">
                    <a class="page-link" href="{{ $data['orders']['last_page_url'] }}" aria-label="Last">
                        <span aria-hidden="true">&raquo;</span>
                        <span class="sr-only">В конец</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
    @endif
    @endif
</main>
@endsection