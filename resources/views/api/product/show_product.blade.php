@extends('api.layouts.main')
@section('content')

<div class="page-heading {{$product['category']['title']}} header-text">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <div class="text-content">
          <h4>{{$product['category']['title_rus']}}</h4>
          <h2>LUMOS</h2>
        </div>
      </div>
    </div>
  </div>
</div>

<main class="cd-main-content">
  <br><a class="nav-link" style="text-align: center" href="{{ route('api.products_api', [$product['category']['title'], 'page' => $page]) }}">Назад</a>
  <div class="container">
    <h4 style="text-align: center; font-weight: bold; padding: 20px;">{{$product['title']}}</h4><br>
    <div class="row align-items-start">
      <div class="col-md-6">
        <div class="product-item" style="text-align: center">
          @include('api.components.carousel')
        </div>
      </div>
      <div class="col-md-6">
        <div class="product-item" style="text-align: center">
          <table class="table table-bordered table-hover ">
            <tbody>
              @foreach ($product['property_values'] as $property => $value)
              <tr>
                <td style="text-align: left">{{ $property }}</td>
                <td style="text-align: right">{{ $value }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
          @include('api.components.qty')
          @include('api.components.rating')
        </div>
      </div>
      <p style="text-align:left">{{$product['description']}}</p>
    </div>
  </div>
</main>


@endsection