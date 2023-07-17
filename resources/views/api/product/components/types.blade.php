@if(count($productType['product']['product_types']) > 1)
    <div class="col" style="text-align:center">
        @isset($productType['product']['description'])
            <br> Другие виды <br> <br>
        @endisset
        @foreach($productType['product']['product_types'] as $type)
            <a href="{{ route('api.product', $type['id']) }}">
                <!-- если это show то крупнее  -->
                <img style="{{empty($productType['product']['description']) ? 'width:80px;' : 'width:150px;'}}; opacity:{{$type['is_published'] == 0 ? '0.3' : '1'}}; border:{{$productType['id'] == $type['id'] ? 'solid' : ''}}"
                    src="{{asset('/storage/'.$type['preview_image'])}}">
            </a>
        @endforeach
    </div>
@endif
