<form action="{{ route('api.addToCart') }}" method="post">
    @csrf

    @empty($total_price)
        <!-- это не корзина -->
        <h4 style="padding:7px">{{$product_type['price'] . ' руб.'}}</h4>
    @endempty
    <div class="qty">
    <span class="input-group-btn">
      <button type="button" class="btn btn-default btn-number" data-type="minus"
              data-field="addToCart[{{ $product_type['id'] }}]">
        <span class="minus bg-dark">-</span>
      </button>
    </span>
        <input type="number" style="width:100px" name="addToCart[{{ $product_type['id'] }}]" class="count" min="0"
               max="{{$product_type['count']}}"
               value="{{$data['cart'][$product_type['id']] ?? $product_type['amount'] ?? 0}}">
        <span class="input-group-btn">
      <button type="button" class="btn btn-default btn-number" data-type="plus"
              data-field="addToCart[{{ $product_type['id'] }}]">
        <span class="plus bg-dark">+</span>
      </button>
    </span>
    </div>
    <input type="submit" class="btn btn-primary btn-lg {{!empty($total_price) ?: 'btn-block'}}" style="height: 40px"
           value="{{$product_type['count'] == 0 || $product_type['is_published'] == 0  ? 'Нет в наличии' : 'В корзину'}}" @disabled($product_type['count'] == 0 || $product_type['is_published'] == 0)>
</form>
