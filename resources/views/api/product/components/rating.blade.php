<div class="down-content">
    <div class="row" style="padding:10px">
        <div class="col-sm">
            <ul>
                @for($i=1; $i<=5; $i++)
                    <li>
                        <i class="fa fa-star{{$i-1<$productType['product']['rating'] & $productType['product']['rating']<$i ? '-half' : ''}}{{$productType['product']['rating']<$i ? '-o' : ''}}"></i>
                    </li>
                @endfor
                ({{ $productType['product']['countRating'] }})
            </ul>
        </div>

        <div class="col-sm">
            <form action="{{ route('api.liked.toggle', $productType['id']) }}" method="post">
                @csrf
                <button type="submit" class="border-0 bg-transparent" @disabled(!session()->has('user_role'))>
                    <i class="fa fa-heart{{isset($data['liked_ids'][$productType['id']]) || !empty($productType['liked']) ? '': '-o'}}" style="cursor: pointer;"></i>
                </button>
            </form>
        </div>

        <div class="col-sm">
            <li style="list-style-type: none;"><a href="#comments" style="{{isset($productType['product']['description']) ?: 'pointer-events:none; color:black'}}">
                    Отзывы ({{ $productType['product']['countComments'] }})</a></li>
        </div>
    </div>
</div>
