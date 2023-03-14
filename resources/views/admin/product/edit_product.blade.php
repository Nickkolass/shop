@extends('admin.layouts.main')
@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">Редактировать продукт</h1>
      </div><!-- /.col -->
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <a href="{{ route('admin.index_admin') }}">Главная</a>
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
    @if ($errors->any())
    <div class="row">
      <div class="alert alert-danger">
        <ul>
          @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    </div><!-- /.container-fluid -->
    @endif
    <div class="row">
      <form action="{{ route('product.update_product', $product['id']) }}" method="post" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div class="form-group">
          <input type="text" name="title" value="{{ $product['title'] }}" class="form-control" placeholder="Наименование">
        </div>

        <div class="form-group">
          <input type="text" name="description" value="{{ $product['description'] }}" class="form-control" placeholder="Описание">
        </div>
        <div class="form-group">
          <textarea name="content" class="form-control" cols="30" rows="10" placeholder="Контент">{{ $product['content'] }}</textarea>
        </div>
        <div class="form-group">
          <input type="text" name="price" value="{{ $product['price'] }}" class="form-control" placeholder="Цена">
        </div>
        <div class="form-group">
          <input type="text" name="count" value="{{ $product['count'] }}" class="form-control" placeholder="Остаток">
        </div>
        <div class="form-group">
          <input type="text" name="is_published" value="{{ $product['is_published'] }}" class="form-control" placeholder="Опубликовать">
        </div>

        <div class="card">
          <div class="card-body card-block">
            <div class="row form-group">
              <div class="control-group" id="exampleInputFile">
                <label class="control-label" for="exampleInputFile">Выберите заставку</label>
                <div class="controls">
                  <div class="entry input-group upload-input-group">
                    <input name="preview_image" type="file" class="form-control">
                  </div>
                  <label class="control-label" for="fields">Выбранное</label>
                </div>
                <td>
                  <img src="{{ asset('/storage/'.$product['preview_image']) }}" width='70' height='70' class="img img-responsive">
                </td>
              </div>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-body card-block">
            <div class="row form-group">
              <div class="control-group" id="fields">
                <label class="control-label" for="fields">Выберите изображения</label>
                <div class="controls">
                  <div class="entry input-group upload-input-group">
                    <input class="form-control" name="product_images[]" type="file" multiple>
                  </div>
                  <label class="control-label" for="fields">Выбранные</label>
                </div>
                @foreach($product['productImages'] as $img)
                <td>
                  <img src="{{ asset('/storage/'.$img['file_path']) }}" width='70' height='70' class="img img-responsive">
                </td>
                @endforeach
              </div>
            </div>
          </div>
        </div>

        <div class="form-group">
          <select name="category_id" class="form-control select2" style="width: 100%;">
            <option selected="selected" disabled>
              @foreach($categories as $category)
            <option {{$category['id']==$product['category_id'] ? 'selected' : ''}} value="{{$category['id']}}"> {{$category['title_rus']}}</option>
            @endforeach
            </option>
          </select>
        </div>

        <div class="form-group">
          <select name="group_id" class="form-control select2" style="width: 100%;">
            <option selected="selected">
            @foreach($groups as $group)
            <option {{$group['id']==$product['group_id'] ? 'selected' : ''}} value="{{$group['id']}}"> {{$group['title']}}</option>
            @endforeach
            </option>
          </select>
        </div>

        <div class="form-group">
          <select name="tags[]" class="tags" multiple="multiple" data-placeholder="Выберите тег" style="width: 100%;">
            @foreach($tags as $tag)
            <option @foreach($product['tags'] as $productTag) {{ $productTag['id'] == $tag['id'] ? 'selected' : '' }} value="{{$tag['id']}}" @endforeach>
              {{$tag['title']}}
            </option>
            @endforeach
          </select>
        </div>

        <div class="form-group">
          <select name="color_id" class="colors" data-placeholder="Выберите цвет" style="width: 100%;">
          <option selected="selected">
            @foreach($colors as $color)
            <option {{$color['id']==$product['color_id'] ? 'selected' : ''}} value="{{$color['id']}}"> {{$color['title']}}</option>
            </option>
            @endforeach
          </select>
        </div>

        <div class="form-group">
          <input type="submit" class="btn-btn-primary" value="Редактировать">
        </div>

      </form>


    </div>
  </div><!-- /.container-fluid -->
</section>
<!-- /.content -->
@endsection