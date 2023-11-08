@extends('admin.layouts.main')
@section('content')

    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Главная</h1>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Small boxes (Stat box) -->
            <div class="row row-flex">
                <div class="col d-flex">
                    <!-- small box -->
                    <div class="small-box w-100 bg-info">
                        <div class="inner">
                            <h3>Новые заказы ( {{ $data['orders']->count() }} )</h3>
                            <h5>@foreach($data['orders'] as $order)
                                    * <a style="color: inherit" href="{{ route('admin.orders.show', $order->id) }}"
                                         class="small-box-footer">{{'На ' . $order->total_price . ' р.'}}</a><br>
                                @endforeach</h5>
                        </div>
                        <div class="icon">
                            <i class="nav-icon fas fa-shopping-bag"></i>
                        </div>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col d-flex">
                    <!-- small box -->
                    <div class="small-box w-100 bg-success">
                        <div class="inner">
                            <h3>Выручка за {{now()->previous('month')->monthName . "{$data['month_payout']['payout']->revenue} р.
                                ({$data['month_payout']['payout']->count} заказов)"}}</h3>
                            <h5>@foreach($data['month_payout']['orders'] as $order)
                                    * <a style="color: inherit" href="{{ route('admin.orders.show', $order->id) }}"
                                         class="small-box-footer">{{'На ' . $order->total_price}}</a><br>
                                @endforeach</h5>

                        </div>
                        <div class="icon">
                            <i class="nav-icon fa fa-rub"></i>
                        </div>
                    </div>
                </div>
                <!-- ./col -->
            </div>
            <div class="row row-flex">
                <div class="col d-flex">
                    <!-- small box -->
                    <div class="small-box w-100 bg-pink">
                        <div class="inner">
                            <h3>Популярные товары</h3>
                            <h5>@foreach($data['productTypes_liked'] as $product)
                                    * <a style="color: inherit"
                                         href="{{ route('admin.products.show', $product->product_id) }}"
                                         class="small-box-footer">{{$product->title}}</a>
                                    <i class="fa fa-heart"></i> ( {{$product->count_likes}} ) <br>
                                @endforeach</h5>
                        </div>
                        <div class="icon">
                            <i class="nav-icon fa fa-heart"></i>
                        </div>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col d-flex">
                    <!-- small box -->
                    <div class="small-box w-100 bg-blue">
                        <div class="inner">
                            <h3>Рейтинговые товары</h3>
                            <h5>@foreach($data['products_rating'] as $product)
                                    * <a style="color: inherit" href="{{ route('admin.products.show', $product->id) }}"
                                         class="small-box-footer">{{$product->title}}</a>
                                    @for($i=1; $i<=5; $i++)
                                        <i class="fa fa-star{{$i-1<$product->rating & $product->rating<$i ? '-half' : ''}}{{$product->rating<$i ? '-o' : ''}}"></i>
                                    @endfor
                                    ( {{$product->count_rating}} )<br>
                                @endforeach</h5>
                        </div>
                        <div class="icon">
                            <i class="nav-icon fa fa-star"></i>
                        </div>
                    </div>
                </div>
                <!-- ./col -->
            </div>
            <div class="row row-flex">
                <div class="col d-flex">
                    <!-- small box -->
                    <div class="small-box w-100 bg-teal">
                        <div class="inner">
                            <h3>Продаваемые товары</h3>
                            <h5>@foreach($data['productTypes_ordered'] as $product)
                                    * <a style="color: inherit"
                                         href="{{ route('admin.products.show', $product['productType_id']) }}"
                                         class="small-box-footer">{{$product['title']}}</a>
                                    ( {{$product['amount'] . ' шт. ' . ' на ' . $product['price'] . ' р.'}} ) <br>
                                @endforeach</h5>

                        </div>
                        <div class="icon">
                            <i class="nav-icon fas fa-money"></i>
                        </div>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col d-flex">
                    <!-- small box -->
                    <div class="small-box w-100 bg-secondary">
                        <div class="inner">
                            <h3>Неопубликованные товары ( {{$data['unpublished']['count']}} )</h3>
                            <h5>@foreach($data['unpublished']['productTypes'] as $product)
                                    * <a style="color: inherit"
                                         href="{{ route('admin.products.show', $product->product_id) }}"
                                         class="small-box-footer">{{$product->title}}</a><br>
                                @endforeach</h5>

                        </div>
                        <div class="icon">
                            <i class="nav-icon fas fa-tshirt"></i>
                        </div>
                    </div>
                </div>
                <!-- ./col -->
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
@endsection
