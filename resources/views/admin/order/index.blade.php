@php use App\Models\OrderPerformer;use App\Models\User; @endphp
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
                                        @can('role', [User::class, User::ROLE_ADMIN])
                                            <th>Заказ</th>
                                            <th>Заказчик</th>
                                        @endcan
                                        <th>Продавец</th>
                                        <th>Наряд</th>
                                        <th>Статус</th>
                                        <th>Товары</th>
                                        <th>Дата заказа</th>
                                        <th>Срок отправки</th>
                                        <th>Способ доставки, получатель</th>
                                        <th>Стоимость</th>
                                    </tr>
                                    </thead>
                                    <h4 hidden>{{$i = 1}}</h4>
                                    <tbody>
                                    @foreach($orders as $order)
                                        <tr style="text-align: center">
                                            @can('role', [User::class, User::ROLE_ADMIN])
                                                <td><a href="{{ route('client.orders.show', $order->order_id) }}">
                                                        {{ $order->order_id }}</a></td>
                                                <td><a href="{{ route('users.show', $order->user->id) }}">
                                                        {{ $order->user->name }}</a></td>
                                            @endcan
                                            <td>
                                                @can('role', [User::class, User::ROLE_ADMIN])
                                                    <a href="{{ route('users.show', $order->saler->id) }}">{{ $order->saler->name }}</a>
                                                @else
                                                    {{$order->saler->name}}
                                                @endcan
                                            </td>
                                            <td><a href="{{ route('admin.orders.show', $order->id) }}">
                                                    {{ $order->id }}</a></td>
                                            <td>
                                                @if ($order->status == OrderPerformer::STATUS_WAIT_DELIVERY)
                                                    <form action="{{ route('admin.orders.update', $order->id) }}"
                                                          method="post">
                                                        @csrf
                                                        @method('patch')
                                                        <div class="form-group">
                                                            <input type="submit" class="btn-primary"
                                                                   value="Подтвердить отправку">
                                                        </div>
                                                    </form>
                                                @else
                                                    {{$order->getStatusTitleAttribute()}}
                                                @endif
                                            </td>
                                            <td>@foreach($order->productTypes as $productType)
                                                    <a href="{{ route('admin.products.show', $productType['productType_id']) }}">
                                                        <img
                                                            src="{{Storage::url($productType['preview_image'])}}"
                                                            style="height: 50px"></a>
                                                    {{$productType['amount'].' шт.'}}
                                                @endforeach
                                            </td>
                                            <td>{{ $order->created_at }}</td>
                                            <td>До {{ $order->dispatch_time }}</td>
                                            <td style="word-wrap: break-word;min-width: 160px;max-width: 160px; white-space:normal">{{ $order->delivery }}</td>
                                            <td>{{ $order->total_price }}</td>
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
        {{ $orders->links('vendor.pagination.simple-bootstrap-4') }}

    </section>

    <!-- /.content -->
@endsection
