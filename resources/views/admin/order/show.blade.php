@extends('admin.layouts.main')
@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">{{'Заказ № '.$order->id.' от '.$order->created_at}}</h1>

      </div><!-- /.col -->
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <a href="{{ route('admin.index') }}">Главная</a>
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
              <td>{{ $order->id }}</td>
            </tr>

            <tr>
              <td>Дата заказа</td>
              <td>{{ $order->created_at }}</td>
            </tr>
            <tr>
              <td>Статус</td>
              <td>{{ $order->status }}</td>
            </tr>
            <tr>
              <td>Способ доставки, получатель</td>
              <td>{{ $order->delivery }}</td>
            </tr>
            <tr>
              <td>Срок отправки</td>
              <td>До {{ $order->dispatch_time }}</td>
            </tr>
            <tr>
              <td>Стоимость заказа</td>
              <td>{{ $order->total_price }}</td>
            </tr>
            <tr>
              <td style="vertical-align: middle">Товары</td>
              <td>
                <table class="table table-striped">
                  <tbody>
                    @foreach($order->productTypes as $productType)
                    <tr>
                      <td><a href="{{ route('admin.products.show', $productType->id) }}">
                          <img src="{{asset('/storage/'.$productType->preview_image)}}" style="height: 140px"></a></td>
                      <td>Название: {{ $productType->title }}<br>
                        Категория: {{ $productType->category->title_rus }}<br>
                        Количество: {{ $productType->amount }}<br>
                        Стоимость: {{ $productType->price }}<br>
                        Продавец:
                        @if(session('user_role') == 'admin')
                        <a class="linkclass disabled" href="{{ route('users.show', $order->saler->id) }}"> {{ $order->saler->name }} </a><br>
                        <a href="{{ route('api.orders.show', $order->order_id) }}">Перейти к заказу</a>
                        @else
                        {{ $productType->saler->name }}
                        @endif
                      </td>
                      <td>@foreach($productType->optionValues as $option => $value)
                        {{$option . ': '. $value}}<br>
                        @endforeach
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
          <a href="{{ route('admin.support') }}" class="btn btn-primary">Поддержка</a>
        </div>
        <div class="mr-3">
          <form action="{{ route('admin.orders.update', $order->id) }}" method="post">
            @csrf
            @method('patch')
            <div class="form-group">
              <input type="submit" class="btn btn-primary" value="Подтвердить отправку" @disabled($order->status != 'В работе')>
            </div>
          </form>
        </div>
        <form action="{{route('admin.orders.destroy', $order->id) }}" method="post">
          @csrf
          @method('delete')
          <input type="submit" class="btn btn-danger" value="Отказаться" @disabled($order->status != 'В работе')>
        </form>
      </div>
    </div>
  </div><!-- /.container-fluid -->
</section>

<!-- /.content -->
@endsection