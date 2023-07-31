<form action="{{ route('api.addToCart') }}" method="post">
    @csrf

    @empty($totalPrice)
        <!-- это не корзина -->
        <h4 style="padding:7px">{{$productType['price'] . ' руб.'}}</h4>
    @endempty
    <div class="qty">
    <span class="input-group-btn">
      <button type="button" class="btn btn-default btn-number" data-type="minus"
              data-field="addToCart[{{ $productType['id'] }}]">
        <span class="minus bg-dark">-</span>
      </button>
    </span>
        <input type="number" style="width:100px" name="addToCart[{{ $productType['id'] }}]" class="count" min="0"
               max="{{$productType['count']}}"
               value="{{$data['cart'][$productType['id']] ?? $productType['amount'] ?? 0}}">
        <span class="input-group-btn">
      <button type="button" class="btn btn-default btn-number" data-type="plus"
              data-field="addToCart[{{ $productType['id'] }}]">
        <span class="plus bg-dark">+</span>
      </button>
    </span>
    </div>
    <input type="submit" class="btn btn-primary btn-lg {{!empty($totalPrice) ?: 'btn-block'}}" style="height: 40px"
           value="{{$productType['count'] == 0 || $productType['is_published'] == 0  ? 'Нет в наличии' : 'В корзину'}}" @disabled($productType['count'] == 0 || $productType['is_published'] == 0)>
</form>
