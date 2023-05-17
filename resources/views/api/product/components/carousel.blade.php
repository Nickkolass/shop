<div id="carouselExampleIndicators{{$productType['id']}}" class="carousel slide" data-ride="carousel">
  <ol class="carousel-indicators">
    <li data-target="#carouselExampleIndicators{{$productType['id']}}" data-slide-to="0" class="active"></li>
    @for ($i=1; $i<=count($productType['product_images']); $i++) <li data-target="#carouselExampleIndicators{{$productType['id']}}" data-slide-to="{{$i}}">
      </li>
      @endfor
  </ol>
  <div class="carousel-inner">
    <div class="carousel-item active">
      <a href="{{ route('api.product', [$productType['product']['category']['title'] ?? $data['category']['title'], $productType['id']]) }}">
        <img src="{{asset('/storage/'.$productType['preview_image'])}}" style="opacity:{{$productType['is_published'] == 0 || $productType['count'] == 0 ? '0.3' : '1'}}" class="d-block w-100">
      </a>
    </div>
    @foreach ($productType['product_images'] as $img)
    <div class="carousel-item">
      <a href="{{ route('api.product', [$productType['product']['category']['title'] ?? $data['category']['title'], $productType['id']]) }}">
        <img src="{{asset('/storage/'.$img)}}" style="opacity:{{$productType['is_published'] == 0 || $productType['count'] == 0 ? '0.3' : '1'}}" class="d-block w-100">
      </a>
    </div>
    @endforeach
  </div>
  <button class="carousel-control-prev" type="button" data-target="#carouselExampleIndicators{{$productType['id']}}" data-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="sr-only">Предыдущий</span>
  </button>
  <button class="carousel-control-next" type="button" data-target="#carouselExampleIndicators{{$productType['id']}}" data-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="sr-only">Следующий</span>
  </button>
</div>