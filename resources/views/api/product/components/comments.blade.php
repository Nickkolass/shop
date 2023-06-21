<div class="down-content" id="comments">
    <h4 style="text-align: center; padding: 10px"> Комментарии </h4>

    @include('admin.product.errors')

    @if(session()->has('user_role'))
        <form action="{{route('api.comment.store', $productType['product_id'])}}" method="post"
              enctype="multipart/form-data">
            @csrf
            <div class="rating-area">
                @for($i = 1; $i<=5; $i++)
                    <input type="radio" name="rating" id="star-{{$i}}" value="{{6-$i}}">
                    <label for="star-{{$i}}" title="Оценка «{{6-$i}}»"></label>
                @endfor
            </div>
            <textarea name="message" id="summernote"> {{old('message')}} </textarea>
            <input type="submit" class="btn btn-primary btn-lg">
        </form>
    @else
        <h4 style="text-align: center; padding: 10px">Чтобы оставить комментарий и оценить товар зарегистрируйтесь или
            войдите. </h4>
    @endif

    @if(!empty($productType['product']['ratingAndComments']))
        @foreach( $productType['product']['ratingAndComments'] as $comment)
            <br>
            <div class="card">
                <div class="card-header">
                    {{$comment['user'] . ', ' . $comment['created_at']}}
                    @for($i=1; $i<=5; $i++)
                        <i class="fa fa-star{{$i-1<$productType['product']['rating'] & $productType['product']['rating']<$i ? '-half' : ''}}{{$productType['product']['rating']<$i ? '-o' : ''}}"></i>
                    @endfor
                ({{ $productType['product']['countRating'] }})
                </div>
                @if(!empty($comment['message']))
                    <div class="card-body">
                        <p class="card-text">{!! $comment['message'] !!}</p>
                    </div>
                @endif
            </div>
        @endforeach
    @endif
</div>

