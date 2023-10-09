<div id="carouselExampleIndicators{{$product_type['id']}}" class="carousel slide" data-ride="carousel">
    <ol class="carousel-indicators">
        <li data-target="#carouselExampleIndicators{{$product_type['id']}}" data-slide-to="0" class="active"></li>
        @for ($i=1; $i<=count($product_type['product_images']); $i++)
            <li data-target="#carouselExampleIndicators{{$product_type['id']}}" data-slide-to="{{$i}}">
            </li>
        @endfor
    </ol>
    <div class="carousel-inner">
        <div class="carousel-item active">
            <a href="{{ route('client.products.show', $product_type['id']) }}">
                <img src="{{Storage::url($product_type['preview_image'])}}"
                     style="opacity:{{$product_type['is_published'] == 0 || $product_type['count'] == 0 ? '0.6' : '1'}}"
                     class="d-block w-100">
            </a>
        </div>
        @foreach ($product_type['product_images'] as $img)
            <div class="carousel-item">
                <a href="{{ route('client.products.show', $product_type['id']) }}">
                    <img src="{{Storage::url($img)}}"
                         style="opacity:{{$product_type['is_published'] == 0 || $product_type['count'] == 0 ? '0.3' : '1'}}"
                         class="d-block w-100">
                </a>
            </div>
        @endforeach
    </div>
    <button class="carousel-control-prev" type="button" data-target="#carouselExampleIndicators{{$product_type['id']}}"
            data-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="sr-only">Предыдущий</span>
    </button>
    <button class="carousel-control-next" type="button" data-target="#carouselExampleIndicators{{$product_type['id']}}"
            data-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="sr-only">Следующий</span>
    </button>
</div>
