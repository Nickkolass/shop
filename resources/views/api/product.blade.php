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

  <div class="latest-products">
    <div class="container">
      <div class="row align-items-start">
        <div class="col-md-6">
          <div class="product-item" style="text-align: center">
            <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
              <ol class="carousel-indicators">
                <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
                @for($i=1; $i<=count($data['product']['product_images']); $i++) <li data-target="#carouselExampleIndicators" data-slide-to="{{$i}}">
                  </li>
                  @endfor
              </ol>
              <div class="carousel-inner">
                <div class="carousel-item active">
                  <img src="{{asset('/storage/'.$data['product']['preview_image'])}}" class="d-block w-100">
                </div>
                @foreach ($data['product']['product_images'] as $product_image)
                <div class="carousel-item">
                  <img src="{{asset('/storage/'.$product_image)}}" class="d-block w-100">
                </div>
                @endforeach
              </div>
              <button class="carousel-control-prev" type="button" data-target="#carouselExampleIndicators" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Предыдущий</span>
              </button>
              <button class="carousel-control-next" type="button" data-target="#carouselExampleIndicators" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Следующий</span>
              </button>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="product-item" style="text-align: center">
            <h4>{{$data['product']['title']}}</h4><br>
            <p style="text-align:left">{{$data['product']['description']}}</p>
            <div class="qty mt-5">
              <form action="{{ route('api.addToCart_api') }}" method="post">
                @csrf
                <span class="input-group-btn">
                  <button type="button" class="btn btn-default btn-number" data-type="minus" data-field="addToCart[{{$data['product']['id']}}]">
                    <span class="minus bg-dark">-</span>
                  </button>
                </span>
                <input type="number" name="addToCart[{{$data['product']['id']}}]" class="count" min="0" max="{{$data['product']['count']}}" value="{{isset(session('cart')[$data['product']['id']]) ? session('cart')[$data['product']['id']] : 0}}">
                <span class="input-group-btn">
                  <button type="button" class="btn btn-default btn-number" data-type="plus" data-field="addToCart[{{$data['product']['id']}}]">
                    <span class="plus bg-dark">+</span>
                  </button>
                </span>
                <div class="product-item" style="text-align: center">
                  <h6>{{$data['product']['price']}} руб.</h6>
                </div>
                <input type="submit" class="btn btn-primary btn-lg btn-block" style="height: 40px" value="В корзину">
              </form>
            </div>
            <div class="down-content" style="text-align:left">
              <ul class="stars">
                @for($i=1; $i<=rand(3, 5); $i++) <li><i class="fa fa-star"></i></li>@endfor
                  <span>Отзывы ({{rand(5, 25)}})</span>
              </ul>
            </div>
            <div style="text-align: center">
              <h4>Другие виды товара</h4><br>
              <ul class="nav nav-pills">
                @foreach($data['product']['group'] as $prod)
                @if($prod['id'] == $data['product']['id'])
                @continue
                @else
                <li class="nav-item">
                  <a class="nav-link" href="{{ route('api.show_api', [$data['category']['title'], $prod['id']]) }}">
                    <img src="{{asset('/storage/'.$prod['preview_image'])}}" class="d-block w-30"></a>
                </li>
                @endif
                @endforeach
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</main>


@endsection