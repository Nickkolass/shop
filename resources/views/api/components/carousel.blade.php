<div id="carouselExampleIndicators{{$product['id']}}" class="carousel slide" data-ride="carousel">
  <ol class="carousel-indicators">
    <li data-target="#carouselExampleIndicators{{$product['id']}}" data-slide-to="0" class="active"></li>
    @for ($i=1; $i<=count($product['product_images']); $i++) <li data-target="#carouselExampleIndicators{{$product['id']}}" data-slide-to="{{$i}}">
      </li>
      @endfor
  </ol>
  <div class="carousel-inner">
    <div class="carousel-item active">
      <a href="{{ route('api.product_api', [$product['category']['title'] ?? $data['category']['title'], $product['id']]) }}">
        <img src="{{asset('/storage/'.$product['preview_image'])}}" class="d-block w-100">
      </a>
    </div>
    @foreach ($product['product_images'] as $img)
    <div class="carousel-item">
      <a href="{{ route('api.product_api', [$product['category']['title'] ?? $data['category']['title'], $product['id']]) }}">
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