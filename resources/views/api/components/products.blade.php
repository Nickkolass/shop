<div class="latest-products">
  <div class="container-fluid">
    <div class="row">
      @foreach ($data['products']['data'] ?? $data['products'] as $product)
      <div class="col-md-3" style="opacity:{{$product['is_published'] == 0 ? '0.5' : '1'}}">
        <div class="product-item" style="text-align:center">
          <h4>{{$product['title']}}</h4><br>
          @include('api.components.carousel')
          @include('api.components.rating')
          @include('api.components.qty')
        </div>
      </div>
      @endforeach
    </div>
  </div>
</div>