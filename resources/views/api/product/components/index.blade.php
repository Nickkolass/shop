<div class="latest-products">
    <div class="container-fluid">
        @foreach ($product_types['data'] ?? $product_types as $key => $product_type)
            @if(($key % 4) == 0)
                <div class="row">
                    @endif
                    <div class="col-md-3">
                        <div class="product-item" style="text-align:center">
                            <h4>{{$product_type['product']['title']}}</h4><br>
                            @include('api.product.components.carousel')
                            @include('api.product.components.rating')
                            @include('api.product.components.types')
                            @include('api.product.components.qty')
                        </div>
                    </div>
                    @if((($key+1) % 4) == 0)
                </div>
            @endif
        @endforeach
    </div>
</div>
