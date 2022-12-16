@extends('layouts.main_layout')
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
        <a href="{{ route('main.index_main') }}">Главная</a>
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
      <form action="{{ route('product.store_product') }}" method="post" enctype="multipart/form-data">

        @csrf

        <div class="form-group">
          <input type="text" name="title" class="form-control" placeholder="Наименование">
        </div>
        <div class="form-group">
          <input type="text" name="descriprion" class="form-control" placeholder="Описание">
        </div>
        <div class="form-group">
          <textarea name="content" class="form-control" cols="30" rows="10" placeholder="Контент"></textarea>
        </div>
        <div class="form-group">
          <input type="text" name="price" class="form-control" placeholder="Цена">
        </div>
        <div class="form-group">
          <input type="text" name="count" class="form-control" placeholder="Остаток">
        </div>
        <div class="form-group">
          <input type="text" name="is_published" class="form-control" placeholder="Опубликовать">
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
          <select name="category_id" class="form-control select2" style="width: 100%;">
            <option selected="selected" disabled>
              @foreach($categories as $category)
            <option value="{{ $category->id }}">{{ $category->title }}</option>
            @endforeach
            </option>
          </select>
        </div>

        <div class="form-group">
          <select name="group_id" class="form-control select2" style="width: 100%;">
            <option selected="selected" disabled>
              @foreach($groups as $group)
            <option value="{{ $group->id }}">{{ $group->title }}</option>
            @endforeach
            </option>
          </select>
        </div>

        <div class="form-group">
          <select name="tags[]" class="tags" multiple="multiple" data-placeholder="Выберите теги" style="width: 100%;">
            @foreach($tags as $tag)
            <option value="{{ $tag->id }}">{{ $tag->title }}</option>
            @endforeach
          </select>
        </div>

        <div class="form-group">
          <select name="colors[]" class="colors" multiple="multiple" data-placeholder="Выберите цвет" style="width: 100%;">
            @foreach($colors as $color)
            <option value="{{ $color->id }}">{{ $color->title }}</option>
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