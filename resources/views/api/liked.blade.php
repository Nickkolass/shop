@extends('api.layouts.main')
@section('content')

    <div class="page-heading liked header-text">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="text-content">
                        <h4>Отложенное</h4>
                        <h2>LUMOS</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <main class="cd-main-content">


        @if(empty($product_types))
            <h4 style="text-align:center">Вы пока не отмечали товары понравившимися</h4>
        @else
            @include('api.product.components.index')
        @endif

    </main>
@endsection
