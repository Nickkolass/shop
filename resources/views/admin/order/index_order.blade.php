@extends('admin.layouts.main')
@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">Заказы</h1>
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
      @if (empty($orders))
      <div style="text-align: center">
        Пока заказов нет
      </div>
      @else
      <div class="col-12">
        <div class="card">
          <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
              <thead>
                <tr style="text-align: center">
                  <th>№ п/п</th>
                  @if(auth()->user()->role == 'admin')
                  <th>Продавец</th>
                  <th>Заказчик</th>
                  @endif
                  <th>Товары</th>
                  <th>Дата заказа</th>
                  <th>Дата отправки</th>
                  <th>Способ доставки, получатель</th>
                  <th>Стоимость заказа</th>
                </tr>
              </thead>
              <h4 hidden>{{$i = 1}}</h4>
              <tbody>
                @foreach($orders as $order)
                <tr style="text-align: center">
                  <td>{{ $i++ }}</td>
                  @if(auth()->user()->role == 'admin')
                  <td><a href="{{ route('user.show_user', $order['saler']['id']) }}">
                      {{ $order['saler']['name'] }}</a></td>
                  <td><a href="{{ route('user.show_user', $order['user']['id']) }}">
                      {{ $order['user']['name'] }}</a></td>
                  @endif
                  <td>@foreach($order['products'] as $product)
                    <a href="{{ route('product.show_product', $product['id']) }}">
                      <img src="{{asset('/storage/'.$product['preview_image'])}}" style="height: 50px"></a>
                    @endforeach
                  </td>
                  <td>{{ $order['created_at'] }}</td>
                  <td>{{ $order['dispatch_time'] }}</td>
                  <td style="word-wrap: break-word;min-width: 160px;max-width: 160px; white-space:normal">{{ $order['delivery'] }}</td>
                  <td>{{ $order['total_price'] }}</td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>

        </div>
      </div>
      @endif
    </div>
  </div><!-- /.container-fluid -->
</section>

<!-- /.content -->
@endsection