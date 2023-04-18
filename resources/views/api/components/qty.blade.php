<form action="{{ route('api.addToCart_api') }}" method="post">
  @csrf

  @if(isset($i))
  <!-- это корзина -->
  <input name="addToCart[{{ $product['id'] }}][product_id]" value="{{ $product['id'] }}" hidden>
  <input name="addToCart[{{ $product['id'] }}][cart_id]" value="{{ $product['cart_id'] }}" hidden>
  @foreach($product['optionValues'] as $optionValue)
  <input name="addToCart[{{ $product['id'] }}][optionValues][{{ $optionValue['option_id'] }}]" value="{{$optionValue['id']}}" hidden>
  @endforeach
  @else
  @include('api.components.option_values')
  <h4 hidden>{{$i = 0}}</h4>
  <h4 style="padding:7px">{{$product['price'] . 'руб.'}}</h4>
  @endif
  <div class="qty">
    <span class="input-group-btn">
      <button type="button" class="btn btn-default btn-number" data-type="minus" data-field="addToCart[{{ $product['id'] }}][amount][{{$i}}]">
        <span class="minus bg-dark">-</span>
      </button>
    </span>
    <input type="number" style="width:100px" name="addToCart[{{ $product['id'] }}][amount][{{$i}}]" class="count" min="0" max="{{$product['count']}}" value="{{$product['inCart']['amount'] ?? $product['amount'] ?? 0}}">
    <span class="input-group-btn">
      <button type="button" class="btn btn-default btn-number" data-type="plus" data-field="addToCart[{{ $product['id'] }}][amount][{{$i}}]">
        <span class="plus bg-dark">+</span>
      </button>
    </span>
  </div>
  <input type="submit" class="btn btn-primary btn-lg {{$i != 0 ?: 'btn-block'}}" style="height: 40px" value="{{$product['count']!==0 ? 'В корзину' : 'Нет в наличии'}}" @disabled($product['count']==0)>
</form>