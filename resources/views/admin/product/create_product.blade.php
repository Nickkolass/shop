@extends('admin.layouts.main')
@section('content')

<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">Добавить продукт</h1>
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
      <form action="{{ route('product.store_product') }}" method="post" enctype="multipart/form-data">

        @csrf

        <div class="form-group">
          <input type="text" name="title" value="{{ old('title') }}" class="form-control" placeholder="Наименование">
        </div>
        <div class="form-group">
          <input type="text" name="description" value="{{ old('description') }}" class="form-control" placeholder="Описание">
        </div>
        <div class="form-group">
          <textarea name="content" value="{{ old('content') }}" class="form-control" cols="30" rows="10" placeholder="Контент"></textarea>
        </div>
        <div class="form-group">
          <input type="text" name="price" value="{{ old('price') }}" class="form-control" placeholder="Цена">
        </div>
        <div class="form-group">
          <input type="text" name="count" value="{{ old('count') }}" class="form-control" placeholder="Остаток">
        </div>
        <div class="form-group">
          <input type="text" name="is_published" value="{{ old('is_published') }}" class="form-control" placeholder="Опубликовать">
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
                </div>
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
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="form-group">
          <select name="group_id" class="form-control select2" style="width: 100%;" data-placeholder="Выберите группу продуктов">
            <option selected="selected">
              @foreach($groups as $group)
            <option value="{{ $group['id'] }}">{{ $group['title'] }}</option>
            @endforeach
            </option>
          </select>
        </div>

        <div class="form-group">
          <select name="category_id" class="form-control select2" style="width: 100%;" data-placeholder="Выберите категорию">
            <option selected="selected" disabled>
               @foreach($categories as $category)
              <option value="{{ $category['id'] }}">{{ $category['title_rus'] }}</option>
              @endforeach
            </option>
          </select>
        </div>

        <div class="form-group">
          <select name="tags[]" class="tags" multiple="multiple" data-placeholder="Выберите теги" style="width: 100%;">
            @foreach($tags as $tag)
            <option value="{{ $tag['id'] }}">{{ $tag['title'] }}</option>
            @endforeach
          </select>
        </div>

        <div class="form-group">
          <select name="color_id" class="colors" data-placeholder="Выберите цвет" style="width: 100%;">
            @foreach($colors as $color)
            <option value="{{ $color['id'] }}">{{ $color['title'] }}</option>
            @endforeach
          </select>
        </div>

        <div class="form-group">
          <input type="submit" class="btn-btn-primary" value="Добавить">
        </div>

      </form>

    </div><!-- /.container-fluid -->
  </div>

</section>
<!-- /.content -->

@endsection