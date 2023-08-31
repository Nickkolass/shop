@if(count($product_type['product']['product_types']) > 1)
    <div class="col" style="text-align:center">
        @isset($product_type['product']['description'])
            <br> Другие виды <br> <br>
        @endisset
        @foreach($product_type['product']['product_types'] as $type)
            <a href="{{ route('client.products.show', $type['id']) }}">
                <!-- если это show то крупнее  -->
                <img
                    style="{{empty($product_type['product']['description']) ? 'width:70px;' : 'width:150px;'}}; opacity:{{$type['is_published'] == 0 ? '0.3' : '1'}}; border:{{$product_type['id'] == $type['id'] ? 'solid' : ''}}"
                    src="{{asset('/storage/'.$type['preview_image'])}}">
            </a>
        @endforeach
    </div>
@endif
