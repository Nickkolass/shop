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
    <form action="{{ route('product.store_product') }}" method="post" enctype="multipart/form-data">
      @csrf
      <div class="row align-items-start">
        <div class="col">

          <!-- Small boxes (Stat box) -->
          @if ($errors->any())
          <div class="row" style="margin:5px">
            <div class="alert alert-danger">
              <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          </div>
          @endif
          <div class="row" style="margin:5px">
            <div class="col" style="padding: 5px;">
              <label>Наименование</label>
              <input type="text" name="title" value="{{ old('title') }}" class="form-control">
            </div>
            <div class="w-100"></div>

            <div class="col" style="padding: 5px;">
              <label>Описание</label>
              <textarea name="description" class="form-control" rows="5">{{ old('description') }}</textarea>
            </div>
            <div class="w-100"></div>


            <div class="col" style="padding: 5px;">
              <label>Цена</label>
              <input type="number" name="price" value="{{ old('price') }}" class="form-control">
            </div>
            <div class="w-100"></div>

            <div class="col" style="padding: 5px;">
              <label>Остаток</label>
              <input type="number" name="count" value="{{ old('count') }}" class="form-control">
            </div>
            <div class="w-100"></div>

            <div class="col" style="padding: 5px;">
              <label class="control-label" for="exampleInputFile">Выберите заставку</label>
              <div class="card">
                <div class="card-body card-block">
                  <div class="row form-group">
                    <div class="control-group" id="exampleInputFile">
                      <div class="controls">
                        <div class="entry input-group upload-input-group">
                          <input name="preview_image" type="file" class="form-control">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="w-100"></div>

            <div class="col" style="padding: 5px;">
              <label class="control-label" for="fields">Выберите изображения</label>
              <div class="card">
                <div class="card-body card-block">
                  <div class="row form-group">
                    <div class="control-group" id="fields">
                      <div class="controls">
                        <div class="entry input-group upload-input-group">
                          <input class="form-control" name="productImages[]" type="file" multiple>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="w-100"></div>

            <div class="col" style="padding: 5px;">
              <label>Категория</label>
              <select name="category_id" id="category_id" class="categories" style="width: 100%;">
                <option selected disabled>Категория</option>
                @foreach($categories as $category)
                <option value="{{ $category['id'] }}" @selected(old('category_id')==$category['id'])>{{ $category['title_rus'] }}</option>
                @endforeach
              </select>
            </div>
            <div class="w-100"></div>

            <div class="col" style="padding: 5px;">
              <label>Группа</label>
              <select name="group_id" class="groups" style="width: 100%;">
                <option selected="selected">
                  @foreach($groups as $group)
                <option value="{{ $group['id'] }}" @selected(old('group_id')==$group['id'])>{{ $group['title'] }}</option>
                @endforeach
                </option>
              </select>
            </div>
            <div class="w-100"></div>

            <div class="col" style="padding: 5px;">
              <label>Теги</label>
              <select name="tags[]" class="tags" multiple="multiple" style="width: 100%;">
                @foreach($tags as $tag)
                <option value="{{ $tag['id'] }}" @selected(in_array($tag['id'], old('tags') ?? []))>{{ $tag['title'] }}</option>
                @endforeach
              </select>
            </div>
            <div class="w-100"></div>

          </div>
        </div>

        <div class="col">
          <div class="row" style="margin:5px">
            <br>
            <h5 style="margin:10px">Опции</h5>
            <div class="w-100"></div>

            @foreach($optionValues as $option => $optionValues)
            <div class="col" style="padding: 5px;">
              <label>{{$option}}</label>
              <select name="optionValues[]" class="tags" multiple="multiple" style="width: 100%;">
                @foreach($optionValues as $optionValue)
                <option value="{{ $optionValue['id'] }}" @selected(in_array($optionValue['id'], old('optionValues') ?? []))>{{ $optionValue['value'] }}</option>
                @endforeach
              </select>
            </div>
            <div class="w-100"></div>
            @endforeach

            <br>
            <h5 style="margin:10px">Характеристики</h5>
            <div class="w-100"></div>

            @foreach($categories[0]['properties'] as $property)
            <div class="col" style="padding: 5px;">
              <label>{{$property['title']}}</label>
              <input type="text" name="propertyValues[{{$property['id']}}]" value="{{ old('propertyValues')[$property['id']] ?? '' }}" class="form-control">
            </div>
            <div class="w-100"></div>
            @endforeach

          </div>
        </div>
      </div><!-- /.container-fluid -->
      <div class="col" style="padding: 5px;">
        <input type="submit" class="btn-btn-primary" value="Добавить">
      </div>
    </form>
  </div>
</section>
<!-- /.content -->

@endsection