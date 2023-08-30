@extends('api.layouts.main')
@section('content')

    <div class="page-heading {{$data['category']['title']}} header-text">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="text-content">
                        <h4>{{$data['category']['title_rus']}}</h4>
                        <h2>LUMOS</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <main class="cd-main-content">
        @include('api.product.components.filter')
        <div>
            <ul class="nav justify-content-center">
                @foreach ($categories as $cat)
                    <li class="nav-item">
                        <a class="nav-link"
                           href="{{ route('api.products.filter', $cat['title']) }}">{{ $cat['title_rus'] }}</a>
                    </li>
                @endforeach
            </ul>
        </div>

        @include('api.product.components.index')

        @if(empty($product_types['data']))
            <h4 style="text-align:center">По вашему запросу товаров не найдено</h4>
        @else
            @include('api.product.components.paginate')
        @endif

    </main>
@endsection
