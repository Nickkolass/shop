<div class="down-content" id="comments">
    <h4 style="text-align: center; padding: 10px"> Комментарии </h4>

    @include('admin.product.errors')

    @if(session()->has('user'))
        <form action="{{route('api.comment.store', $productType['product_id'])}}" method="post"
              enctype="multipart/form-data">
            @csrf
            <div class="rating-area">
                @for($i = 1; $i<=5; $i++)
                    <input type="radio" name="rating" id="star-{{$i}}" value="{{6-$i}}">
                    <label for="star-{{$i}}" title="Оценка «{{6-$i}}»"></label>
                @endfor
            </div>

            <div class="mr-auto" style="text-align:center;">
                <div class="card">
                    <div class="card-body card-block">
                        <div class="row form-group">
                            <div class="control-group">
                                <div class="controls">
                                    <div class="entry input-group upload-input-group">
                                        <input class="form-control" name="commentImages[]" type="file" multiple>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <textarea name="message" id="summernote"> {{old('message')}} </textarea>
            <input type="hidden" name="productType_id" value="{{$productType['id']}}">
            <input type="submit" class="btn btn-primary btn-lg"
                   title="{{!$productType['product']['commentable'] ? 'Вы уже комментировали этот товар' : ''}}" @disabled(!$productType['product']['commentable'])>

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
                    {{$comment['user']['name'] . ', ' . $comment['created_at']}}
                    @for($i=1; $i<=5; $i++)
                        <i class="fa fa-star{{$i-1<$comment['rating'] & $comment['rating']<$i ? '-half' : ''}}{{$comment['rating']<$i ? '-o' : ''}}"></i>
                    @endfor
                ({{ $productType['product']['countRating'] }})
                </div>
                @if(!empty($comment['commentImages']))
                    <div class="card-body" style="margin-inline: 50px">
                        @foreach($comment['commentImages'] as $img)
                            <img src="{{ asset('/storage/'.$img) }}" width='150'
                                 class="img img-responsive">
                        @endforeach
                    </div>
                @endif
                @if(!empty($comment['message']))
                    <div class="card-body">
                        <p class="card-text">{!! $comment['message'] !!}</p>
                    </div>
                @endif
            </div>
        @endforeach
    @endif
</div>

