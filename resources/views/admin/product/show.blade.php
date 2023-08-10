@extends('admin.layouts.main')
@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{$product->title}}</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <a href="{{ route('admin.index') }}">Главная</a>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Small boxes (Stat box) -->
            <div class="row">
                @if(url()->previous() == route('admin.products.editProperties', $product->id) & $product->productTypes->count() != 0)
                    <div class="card" style="background:red">
                        <h4>После редактирования продукта, проверьте соответствие его разновидностей новым
                            классификаторам. <br>
                            Несоответствующие сняты с публикации и недоступны для опубликования до внесения изменений.
                        </h4>
                    </div>
                @endif

                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex p-3">
                            <div class="mr-3">
                                <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-primary">Редактировать</a>
                            </div>

                            <div class="mr-3">
                                <h4 hidden> {{ $block = ($product->productTypes->count() == collect([1])->crossjoin(...$product->optionValues)->count() || $product->optionValues->count() == 0) ? 1 : 0}} </h4>
                                <a href="{{ route('admin.productTypes.create', $product->id) }}" class="btn btn-primary {{$block ? 'disabled' : ''}}" title="{{$block ? 'Добавлено максимальное количество типов по выбранным классификаторам' : ''}}">Добавить тип</a>
                            </div>

                            <div class="mr-3">
                                <form action="{{route('admin.products.publish', $product->id) }}" method="post">
                                    @csrf
                                    @method('patch')
                                    <h4 hidden> {{ $publish = $product->productTypes->where('is_published', 0)->count() > 0 }} </h4>
                                    <input hidden type="checkbox" name="publish" @checked($publish)>
                                    <input type="submit" class="btn btn-primary"
                                           value="{{ $publish ? 'Опубликовать все' : 'Снять с публикации' }}">
                                </form>
                            </div>

                            <div class="mr-3">
                                <form action="{{route('admin.products.destroy', $product->id) }}" method="post">
                                    @csrf
                                    @method('delete')
                                    <input type="submit" class="btn btn-danger" value="Удалить">
                                </form>
                            </div>
                        </div>

                        <div class="card-body table-responsive p-0">
                            <table class="table-sm table-hover text-nowrap">
                                <tbody>
                                <tr>
                                    <td>ID</td>
                                    <td>{{ $product->id }}</td>
                                </tr>

                                @if (session('user.role') == 'admin')
                                    <tr>
                                        <td>Продавец</td>
                                        <td>{{ $product->saler_id }}</td>
                                    </tr>
                                @endif

                                <tr>
                                    <td>Наименование</td>
                                    <td>{{ $product->title }}</td>
                                </tr>
                                <tr>
                                    <td>Описание</td>
                                    <td>{{ $product->description }}</td>
                                </tr>

                                <tr>
                                    <td>Категория</td>
                                    <td>{{ $product->category->title_rus }}</td>
                                </tr>

                                <tr>
                                    <td>Теги</td>
                                    <td>@foreach ($product->tags as $tag)
                                            {{ $tag->title }} <br>
                                        @endforeach</td>
                                </tr>

                                <tr>
                                    <td>Характеристики</td>
                                    <td>
                                        @foreach ($product->propertyValues as $property => $value)
                                            {{$property . ': ' . $value}}<br>
                                        @endforeach
                                    </td>
                                </tr>
                                <tr>
                                    <td>Классификаторы</td>
                                    <td>
                                        @foreach ($product->optionValues as $option => $values)
                                            {{$option . ': '}}
                                            @foreach ($values as $value)
                                                {{$value->value . ', '}}
                                            @endforeach
                                            <br>
                                        @endforeach
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Рейтинг
                                    </td>
                                    <td>
                                        @for($i=1; $i<=5; $i++)
                                            <i class="fa fa-star{{$i-1<$product['rating'] & $product['rating']<$i ? '-half' : ''}}{{$product['rating']<$i ? '-o' : ''}}"></i>
                                        @endfor
                                    ({{ $product['countRating'] }})
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <h4 style="text-align:center">Разновидности<br></h4>

                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover text-nowrap">
                                <thead>
                                <tr style="text-align:center">
                                    <th>ID</th>
                                    <th>Классификаторы</th>
                                    <th>Заставка</th>
                                    <th>Изображения</th>
                                    <th>Цена</th>
                                    <th>Остаток</th>
                                    <th>Добавлен <br> в избранное</th>
                                    <th>Публикация</th>
                                    <th>Редактирование</th>
                                    <th>Удаление</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($product->productTypes as $productType)
                                    <tr style="text-align:center">
                                        <td>{{$productType->id}}</td>
                                        <td>
                                            @foreach ($productType->optionValues as $option => $value)
                                                {{$option . ': ' . $value}}<br>
                                            @endforeach
                                        </td>
                                        <td><img src="{{ asset('/storage/'.$productType->preview_image) }}" width='50'
                                                 height='50' class="img img-responsive"></td>
                                        <td>
                                            @foreach($productType->productImages as $img)
                                                <img src="{{ asset('/storage/'.$img->file_path) }}" width='50'
                                                     height='50' class="img img-responsive">
                                            @endforeach
                                        </td>
                                        <td>{{ $productType->price }}</td>
                                        <td>{{ $productType->count }}</td>
                                        <td>{{ $productType->liked_count }}</td>
                                        <td>
                                            <form
                                                action="{{route('admin.productTypes.publish', $productType->id) }}"
                                                method="post">
                                                @csrf
                                                @method('patch')
                                                <h4 hidden> {{$blockOV = $productType->optionValues->diff($product->optionValues->pluck('*.value')->flatten())->count() != 0}} </h4>
                                                <h4 hidden> {{$blockCount = $productType->count <= 0}} </h4>
                                                <input type="submit" class="btn btn-primary"
                                                       value="{{ $productType->is_published == 0 ? 'Опубликовать' : 'Снять с публикации' }}"
                                                       title="{{$blockOV ? 'Не соответсствует классификаторам товара' : ($blockCount ? 'Недостаточный остаток типа товара' : '')}}"
                                                    @disabled($blockOV || $blockCount)>
                                            </form>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.productTypes.edit', $productType->id) }}"
                                               class="btn btn-primary">Редактировать</a>
                                        </td>
                                        <td>
                                            <form action="{{ route('admin.productTypes.destroy', $productType->id) }}"
                                                method="post">
                                                @csrf
                                                @method('delete')
                                                <input type="submit" class="btn btn-danger" value="Удалить">
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="down-content" id="comments" style="margin-inline: 50px">
                            <h4 style="text-align: center"> Оценки ({{$product->countComments}})</h4>
                            @if(!empty($product->ratingAndComments))
                                @foreach( $product->ratingAndComments as $comment)
                                    <br>
                                    <div class="card" style="margin-inline: 50px">
                                        <div class="card-header" style="margin-inline: 50px">
                                            {{$comment->user->name . ', ' . $comment->created_at->diffForHumans()}}
                                            @for($i=1; $i<=5; $i++)
                                                <i class="fa fa-star{{$i-1<$comment['rating'] & $comment['rating']<$i ? '-half' : ''}}{{$comment['rating']<$i ? '-o' : ''}}"></i>
                                            @endfor
                                        ({{ $product['countRating'] }})
                                        </div>
                                        @if(!empty($comment->commentImages))
                                            <div class="card-body" style="margin-inline: 50px">
                                                @foreach($comment->commentImages as $img)
                                                    <img src="{{ asset('/storage/'.$img->file_path) }}" width='150'
                                                         class="img img-responsive">
                                                @endforeach
                                            </div>
                                        @endif
                                        @if(!empty($comment->message))
                                            <div class="card-body" style="margin-inline: 50px">
                                                <p class="card-text">{!! $comment->message !!}</p>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
@endsection
