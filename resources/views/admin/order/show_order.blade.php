@extends('admin.layouts.main')
@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">{{'Заказ № '.$order['id'].' от '.$order['created_at']}}</h1>

      </div><!-- /.col -->
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <a href="{{ route('admin.index_admin') }}">Главная</a>
        </ol>
      </div><!-- /.col -->
    </div><!-- /.row -->
  </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<!-- Main content -->
<section class="content">
  <div class="container-fluid">
    <!-- Small boxes (Stat box) -->
    <div class="row">
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
              <td>Статус</td>
              <td>
                @if (!empty($order['deleted_at']))
                {{ 'Отменен ' . $order['deleted_at'] }}
                @elseif($order['end'] != '0000-00-00')
                {{ 'Получен заказчиком ' . $order['end']}}
                @elseif ($order['status'] != '0000-00-00')
                {{ 'Отправлен ' . $order['status'] }}
                @else
                В работе
                @endif
              </td>
            </tr>
            <tr>
              <td>Способ доставки, получатель</td>
              <td>{{ $order['delivery'] }}</td>
            </tr>
            <tr>
              <td>Срок отправки</td>
              <td>До {{ $order['dispatch_time'] }}</td>
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
                    @foreach($products as $product)
                    <tr>
                      <td><a href="{{ route('product.show_product', $product['id']) }}">
                          <img src="{{asset('/storage/'.$product['preview_image'])}}" style="height: 140px"></a></td>
                      <td>Название: {{ $product['title'] }}<br>
                        Категория: {{ $product['category']['title_rus'] }}<br>
                        Количество: {{ $product['amount'] }}<br>
                        Стоимость: {{ $product['price'] }}<br>
                        Продавец: 
                        @if(auth()->user()->role == 'admin')
                          <a class="linkclass disabled" href="{{ route('user.show_user', $product['saler_id']) }}"> {{ $product['saler']['name'] }} </a><br>
                          <a href="{{ route('api.orderShow_api', $order['id']) }}">Перейти к заказу</a>
                        @else
                        {{ $product['saler']['name'] }}
                        @endif
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
        <div class="mr-3">
          <a href="{{ route('user.support_user') }}" class="btn btn-primary">Поддержка</a>
        </div>
        <div class="mr-3">
          <form action="{{ route('order.update_order', $order['id']) }}" method="post">
            @csrf
            @method('patch')
            <div class="form-group">
              <input type="submit" class="btn btn-primary" value="Подтвердить отправку" {{$order['status'] != '0000-00-00' ? 'disabled' : ''}} {{ !empty($order['deleted_at']) ? 'disabled' : '' }}>
            </div>
          </form>
        </div>
        <form action="{{route('order.delete_order', $order['id']) }}" method="post">
          @csrf
          @method('delete')
          <input type="submit" class="btn btn-danger" value="Отказаться" {{ !empty($order['deleted_at']) ? 'disabled' : '' }} {{$order['status'] != '0000-00-00' ? 'disabled' : ''}}>
        </form>
      </div>
    </div>
  </div><!-- /.container-fluid -->
</section>

<!-- /.content -->
@endsection