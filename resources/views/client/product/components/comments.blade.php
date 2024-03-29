@php use App\Models\User; @endphp
<div class="down-content" id="comments">
    <h4 style="text-align: center; padding: 10px"> Комментарии </h4>

    @include('admin.product.errors')

    @cannot('verify', User::class)
        <h4 style="text-align: center; padding: 10px">Чтобы оставить комментарий и оценить товар зарегистрируйтесь,
            войдите или подтвердите email. </h4>
    @else
        <form action="{{route('client.comment.store', $product_type['product_id'])}}" method="post"
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
                                        <input class="form-control" name="comment_images[]" type="file" multiple>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <textarea name="message" id="summernote"> {{old('message')}} </textarea>
            <input type="hidden" name="product_id" value="{{$product_type['product_id']}}">
            <input type="hidden" name="user_id" value="{{session('user.id')}}">
            <input type="submit" class="btn btn-primary btn-lg"
                   title="{{!$product_type['product']['commentable'] ? 'Вы уже комментировали этот товар' : ''}}" @disabled(!$product_type['product']['commentable'])>

        </form>
    @endcan

    @if(!empty($product_type['product']['rating_and_comments']))
        @foreach( $product_type['product']['rating_and_comments'] as $comment)
            <br>
            <div class="card">
                <div class="card-header">
                    {{$comment['user']['name'] . ', ' . $comment['created_at']}}
                    @for($i=1; $i<=5; $i++)
                        <i class="fa fa-star{{$i-1<$comment['rating'] & $comment['rating']<$i ? '-half' : ''}}{{$comment['rating']<$i ? '-o' : ''}}"></i>
                    @endfor
                ({{ $product_type['product']['count_rating'] }})
                </div>
                @if(!empty($comment['comment_images']))
                    <div class="card-body" style="margin-inline: 50px">
                        @foreach($comment['comment_images'] as $img)
                            <img src="{{Storage::url( $img) }}" width='150'
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

