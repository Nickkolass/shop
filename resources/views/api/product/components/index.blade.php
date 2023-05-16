<div class="latest-products">
  <div class="container-fluid">
    <div class="row">
      @foreach ($productTypes['data'] ?? $productTypes as $productType)
      <div class="col-md-3">
        <div class="product-item" style="text-align:center">
          <h4>{{$productType['product']['title']}}</h4><br>
          @include('api.product.components.carousel')
          @include('api.product.components.types')
          @include('api.product.components.rating')
          @include('api.product.components.qty')
        </div>
      </div>
      @endforeach
    </div>
  </div>
</div>