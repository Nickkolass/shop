@extends('client.layouts.main')
@section('content')

<div class="page-heading {{$category->title}} header-text">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <div class="text-content">
          <h4>{{$category->title_rus}}</h4>
          <h2>LUMOS</h2>
        </div>
      </div>
    </div>
  </div>
</div>

<main class="cd-main-content">
  @include('client.filter')
  <div>
    <ul class="nav justify-content-center">
      @foreach ($categories as $cat)
      <li class="nav-item">
        <a class="nav-link" href="{{ route('client.product_client', $cat->title) }}">{{ $cat->title_rus }}</a>
      </li>
      @endforeach
    </ul>
  </div>

  <div class="latest-products">
    <div class="container">
      <div class="row">
        @foreach ($products as $product)
        <div class="col-md-4">
          <div class="product-item" style="text-align: center">
            <h4>{{$product->title}}</h4><br>
            <div id="carouselExampleIndicators{{$product->id}}" class="carousel slide" data-ride="carousel">
              <ol class="carousel-indicators">
                <li data-target="#carouselExampleIndicators{{$product->id}}" data-slide-to="0" class="active"></li>
                @for($i=1; $i<=count($productImages[$product->id]); $i++)
                  <li data-target="#carouselExampleIndicators{{$product->id}}" data-slide-to="{{$i}}"></li>
                  @endfor
              </ol>
              <div class="carousel-inner">
                <div class="carousel-item active">
                  <a href="{{ route('client.show_client', [$cat->title, $product->id]) }}">
                    <img src="{{asset('/storage/'.$product['preview_image'])}}" class="d-block w-100">
                  </a>
                </div>
                @foreach ($productImages[$product->id] as $img)
                <div class="carousel-item">
                  <a href="{{ route('client.show_client', [$cat->title, $product->id]) }}">
                    <img src="{{asset('/storage/'.$img['file_path'])}}" class="d-block w-100">
                  </a>
                </div>
                @endforeach
              </div>
              <button class="carousel-control-prev" type="button" data-target="#carouselExampleIndicators{{$product->id}}" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Предыдущий</span>
              </button>
              <button class="carousel-control-next" type="button" data-target="#carouselExampleIndicators{{$product->id}}" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Следующий</span>
              </button>
            </div>
            <div class="qty mt-5">
              <span class="input-group-btn">
                <button type="button" class="btn btn-default btn-number" data-type="minus" data-field="{{$product->id}}">
                  <span class="minus bg-dark">-</span>
                </button>
              </span>
              <input type="number" name="{{$product->id}}" class="count" value="0" min="0" max="{{$product->count}}">
              <span class="input-group-btn">
                <button type="button" class="btn btn-default btn-number" data-type="plus" data-field="{{$product->id}}">
                  <span class="plus bg-dark">+</span>
                </button>
              </span>
              <h6>{{$product->price}} руб.</h6>
            </div>
            <div class="down-content" style="text-align:left">

              <p style="text-align:left">{{$product->description}}</p>
              <ul class="stars">
                @for($i=1; $i<=rand(3, 5); $i++) <li><i class="fa fa-star"></i></li>@endfor
                  <span>Отзывы ({{rand(5, 25)}})</span>

              </ul>
            </div>
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </div>
  <div>
    {{ $products->links('vendor.pagination.bootstrap-4') }}
  </div>
</main>


@endsection