@foreach ($product['option_values'] as $option => $values)
<h4>{{$option}}<br></h4>
<div class="btn-group" data-toggle="buttons" style="padding: 7px">
  @foreach ($values as $value)
  <input name="addToCart[{{ $product['id'] }}][product_id]" value="{{ $product['id'] }}" hidden>
  <label class="btn btn-lg btn-outline-primary" style="margin-left: 5px; margin-right: 5px; width: 100px; height: 30px;">
    <input type="radio" name="addToCart[{{ $product['id'] }}][optionValues][{{ $value['option_id'] }}]" value="{{$value['id']}}" @checked(($product['inCart']['optionValues'][$value['option_id']] ?? null)==$value['id']) required>{{$value['value']}}
  </label>
  @endforeach
</div>
@endforeach