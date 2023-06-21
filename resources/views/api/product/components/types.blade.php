@if(count($productType['product']['product_types']) > 1)
<div class="col" style="text-align:center">
    @foreach($productType['product']['product_types'] as $type)
        <a href="{{ route('api.product', [$productType['product']['category']['title'] ?? $data['category']['title'], $type['id']]) }}">
            <!-- если это show то крупнее  -->
            <img style="{{empty($productType['product']['description']) ? 'height:50px; width:50px' : 'height:120px; width:120px'}}; opacity:{{$type['is_published'] == 0 ? '0.3' : '1'}}; border:{{$productType['id'] == $type['id'] ? 'solid' : ''}}" src="{{asset('/storage/'.$type['preview_image'])}}">
        </a>
    @endforeach
</div>
@endif
