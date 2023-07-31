@extends('api.layouts.main')
@section('content')
    <div class="page-heading {{$productType['product']['category']['title']}} header-text">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="text-content">
                        <h4>{{$productType['product']['category']['title_rus']}}</h4>
                        <h2>LUMOS</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <main class="cd-main-content">
        <div class="container">
            <h4 style="text-align: center; font-weight: bold; padding: 20px;">{{$productType['product']['title']}}</h4>
            <br>
            <div class="row align-items-start">
                <div class="col-md-6">
                    <div class="product-item" style="text-align: center">
                        @include('api.product.components.carousel')
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="product-item" style="text-align: center">
                        <table class="table table-hover ">
                            <tbody>
                            <th colspan="2" style="text-align: center">Выбранный вариант</th>
                            @foreach ($productType['option_values'] as $option => $value)
                                <tr>
                                    <td style="text-align: left">{{ $option }}</td>
                                    <td style="text-align: right">{{ $value }}</td>
                                </tr>
                            @endforeach
                            <th colspan="2" style="text-align: center">Характеристики</th>
                            @foreach ($productType['product']['property_values'] as $property => $value)
                                <tr>
                                    <td style="text-align: left">{{ $property }}</td>
                                    <td style="text-align: right">{{ $value }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        @include('api.product.components.rating')
                        @include('api.product.components.qty')
                    </div>
                </div>
                <p style="text-align:left">{{$productType['product']['description']}}</p>
                @include('api.product.components.types')
            </div>
            @include('api.product.components.comments')
        </div>
    </main>

@endsection
