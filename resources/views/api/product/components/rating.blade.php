<div class="down-content">
    <div class="row" style="padding:10px">
        <div class="col-sm">
            <ul>
                @for($i=1; $i<=5; $i++)
                    <li>
                        <i class="fa fa-star{{$i-1<$product_type['product']['rating'] & $product_type['product']['rating']<$i ? '-half' : ''}}{{$product_type['product']['rating']<$i ? '-o' : ''}}"></i>
                    </li>
                @endfor
                ({{ $product_type['product']['count_rating'] }})
            </ul>
        </div>

        <div class="col-sm">
            <form action="{{ route('api.liked.toggle', $product_type['id']) }}" method="post">
                @csrf
                <button type="submit" class="border-0 bg-transparent"
                        title="{{!session()->has('user') ? 'Для добавления в избранное зарегистрируйтесь или войдите' : ''}}" @disabled(!session()->has('user'))>
                    <i class="fa fa-heart{{isset($data['liked_ids'][$product_type['id']]) || !empty($product_type['likeable']) ? '': '-o'}}"
                       style="cursor: pointer;"></i>
                </button>
            </form>
        </div>

        <div class="col-sm">
            <li style="list-style-type: none;"><a href="#comments"
                                                  style="{{isset($product_type['product']['description']) ?: 'pointer-events:none; color:black'}}">
                    Отзывы ({{ $product_type['product']['count_comments'] }})</a></li>
        </div>
    </div>
</div>
