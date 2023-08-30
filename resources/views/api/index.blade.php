@extends('api.layouts.main')
@section('content')
    <!-- Page Content -->
    <div class="section-heading">
        <div class="banner header-text">
            <div class="owl-banner owl-carousel">
                @foreach ($categories as $category)
                    <div>
                        <a class="nav-link" href="{{ route('api.products.filter', $category['title']) }}"> <img
                                src="{{asset('/storage/view/'.$category['title'].'.jpg')}}" alt="">
                            <div class="text-content">
                                <p><strong>
                                        <h2>{{$category['title_rus']}}</h2>
                                    </strong></p>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    @if(!empty($data['viewed']))
        <div style="text-align: center">
            <h4>Просмотренные товары</h4>
        </div>
        <div class="latest-products">
            <div class="container-fluid">
                <div class="row">
                    @foreach ($data['viewed'] as $product_type)
                        <div class="col-md-3">
                            <div class="product-item" style="text-align:center">
                                <h4>{{$product_type['product']['title']}}</h4><br>
                                @include('api.product.components.carousel')
                                @include('api.product.components.rating')
                                @include('api.product.components.types')
                                @include('api.product.components.qty')
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif


    @if(!empty($data['liked']))
        <div style="text-align: center">
            <h4>Отложенное</h4>
        </div>
        <div class="latest-products">
            <div class="container-fluid">
                <div class="row">
                    @foreach ($data['liked'] as $product_type)
                        <div class="col-md-3">
                            <div class="product-item" style="text-align:center">
                                <h4>{{$product_type['product']['title']}}</h4><br>
                                @include('api.product.components.carousel')
                                @include('api.product.components.rating')
                                @include('api.product.components.types')
                                @include('api.product.components.qty')
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

@endsection
