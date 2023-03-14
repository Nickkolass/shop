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
  @include('api.filter')
  <div>
    <ul class="nav justify-content-center">
      @foreach ($categories as $cat)
      <li class="nav-item">
        <a class="nav-link" href="{{ route('api.products_api', $cat['title']) }}">{{ $cat['title_rus'] }}</a>
      </li>
      @endforeach
    </ul>
  </div>

  <div class="latest-products">
    <div class="container-fluid">
      <div class="row">
        @foreach ($data['products']['data'] as $product)
        
        <div class="col-md-3" style="opacity:{{$product['is_published'] == 0 ? '0.5' : '1'}}">
          <div class="product-item" style="text-align:center">
            <form action="{{ route('api.addToCart_api') }}" method="post">
              @csrf
              <h4>{{$product['title']}}</h4><br>
              <div id="carouselExampleIndicators{{$product['id']}}" class="carousel slide" data-ride="carousel">
                <ol class="carousel-indicators">
                  <li data-target="#carouselExampleIndicators{{$product['id']}}" data-slide-to="0" class="active"></li>
                  @for ($i=1; $i<=count($product['product_images']); $i++) 
                    <li data-target="#carouselExampleIndicators{{$product['id']}}" data-slide-to="{{$i}}"></li>
                  @endfor
                </ol>
                <div class="carousel-inner">
                  <div class="carousel-item active">
                    <a href="{{ route('api.product_api', [$data['category']['title'], $product['id']]) }}">
                      <img src="{{asset('/storage/'.$product['preview_image'])}}" class="d-block w-100">
                    </a>
                  </div>
                  @foreach ($product['product_images'] as $img)
                  <div class="carousel-item">
                    <a href="{{ route('api.product_api', [$data['category']['title'], $product['id']]) }}">
                      <img src="{{asset('/storage/'.$img['file_path'])}}" class="d-block w-100">
                    </a>
                  </div>
                  @endforeach
                </div>
                <button class="carousel-control-prev" type="button" data-target="#carouselExampleIndicators{{$product['id']}}" data-slide="prev">
                  <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                  <span class="sr-only">Предыдущий</span>
                </button>
                <button class="carousel-control-next" type="button" data-target="#carouselExampleIndicators{{$product['id']}}" data-slide="next">
                  <span class="carousel-control-next-icon" aria-hidden="true"></span>
                  <span class="sr-only">Следующий</span>
                </button>
              </div>
              <div class="qty mt-5">
                <span class="input-group-btn">
                  <button type="button" class="btn btn-default btn-number" data-type="minus" data-field="addToCart[{{$product['id']}}]">
                    <span class="minus bg-dark">-</span>
                  </button>
                </span>
                <input type="number" name="addToCart[{{$product['id']}}]" class="count" min="0" max="{{$product['count']}}" value="{{isset($data['cart'][$product['id']]) ? $data['cart'][$product['id']] : 0}}">
                <span class="input-group-btn">
                  <button type="button" class="btn btn-default btn-number" data-type="plus" data-field="addToCart[{{$product['id']}}]">
                    <span class="plus bg-dark">+</span>
                  </button>
                </span>
                <input type="submit" class="btn btn-primary btn-lg" style="height: 35px" value="В корзину">
                <h6>{{$product['price']}} руб.</h6>
              </div>
              <div class="down-content" style="text-align:left">
                <p style="text-align:left">{{$product['description']}}</p>
                <ul class="stars">
                  @for($i=1; $i<=rand(3, 5); $i++) <li><i class="fa fa-star"></i></li>@endfor
                    <span>Отзывы ({{rand(5, 25)}})</span>
                </ul>
              </div>
            </form>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </div>

  @if(empty($data['products']['data']))
  <h4 style="text-align:center">По вашему запросу товаров не найдено</h4>
  @else
  @if($data['products']['last_page'] != 1)
  <div>
    <nav aria-label="Page navigation example">
      <ul class="pagination justify-content-center">
        <li class="page-item {{ $data['products']['current_page'] == 1 ? 'disabled' : ''}}">
          <a class="page-link" href="{{ $data['products']['first_page_url'] }}" aria-label="First">
            <span aria-hidden="true">&laquo;</span>
            <span class="sr-only">В начало</span>
          </a>
        </li>
        <li class="page-item {{ $data['products']['current_page'] == 1 ? 'disabled' : ''}}">
          <a class="page-link" href="{{ $data['products']['prev_page_url'] }}" tabindex="-1">Назад</a>
        </li>
        @foreach ($data['products']['links'] as $link)
        <li class="page-item {{ $link ['active'] == true ? 'active' : ''}}"><a class="page-link" href="{{ $link['url'] }}">{{$link['label']}}</a></li>
        @endforeach
        <li class="page-item {{ $data['products']['last_page'] == $data['products']['current_page'] ? 'disabled' : ''}}">
          <a class="page-link" href="{{ $data['products']['next_page_url'] }}">Вперед</a>
        </li>
        <li class="page-item {{ $data['products']['last_page'] == $data['products']['current_page'] ? 'disabled' : ''}}">
          <a class="page-link" href="{{ $data['products']['last_page_url'] }}" aria-label="Last">
            <span aria-hidden="true">&raquo;</span>
            <span class="sr-only">В конец</span>
          </a>
        </li>
      </ul>
    </nav>
  </div>
  @endif
  @endif
</main>
@endsection