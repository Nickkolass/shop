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
    <form action="{{ route('product.update_product', $product['id']) }}" method="post" enctype="multipart/form-data">
      @csrf
      @method('patch')
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
              <input type="text" name="title" value="{{ $product['title'] }}" class="form-control">
            </div>
            <div class="w-100"></div>

            <div class="col" style="padding: 5px;">
              <label>Описание</label>
              <textarea name="description" class="form-control" rows="5">{{ $product['description'] }}</textarea>
            </div>
            <div class="w-100"></div>

            <div class="col" style="padding: 5px;">
              <label>Цена</label>
              <input type="number" name="price" value="{{ $product['price'] }}" class="form-control">
            </div>
            <div class="w-100"></div>

            <div class="col" style="padding: 5px;">
              <label>Остаток</label>
              <input type="number" name="count" value="{{ $product['count'] }}" class="form-control">
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
                        <label class="control-label" for="fields">Выбранное</label>
                      </div>
                      <td>
                        <img src="{{ asset('/storage/'.$product['preview_image']) }}" width='70' height='70' class="img img-responsive">
                      </td>
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
                        <label class="control-label" for="fields">Выбранные</label>
                      </div>
                      @foreach($product['product_images'] as $img)
                      <td>
                        <img src="{{ asset('/storage/'.$img) }}" width='70' height='70' class="img img-responsive">
                      </td>
                      @endforeach
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="w-100"></div>

            <div class="col" style="padding: 5px;">
              <label>Группа</label>
              <select name="group_id" class="categories" data-placeholder="Группа" style="width: 100%;">
                @foreach($groups as $group)
                <option value="{{ $group['id'] }}" @selected($product['group_id']==$group['id'])>{{ $group['title'] }}</option>
                @endforeach
              </select>
            </div>
            <div class="w-100"></div>

            <div class="col" style="padding: 5px;">
              <label>Теги</label>
              <select name="tags[]" class="tags" multiple="multiple" data-placeholder="Теги" style="width: 100%;">
                @foreach($tags as $tag)
                <option value="{{ $tag['id'] }}" @selected(in_array($tag['id'], $product['tags'] ?? []))>{{ $tag['title'] }}</option>
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
                <option value="{{ $optionValue['id'] }}" @selected(in_array($optionValue['id'], $product['option_values'] ?? []))>{{ $optionValue['value'] }}</option>
                @endforeach
              </select>
            </div>
            <div class="w-100"></div>
            @endforeach

            <br>
            <h5 style="margin:10px">Характеристики</h5>
            <div class="w-100"></div>

            @foreach($product['category']['properties'] as $property)
            <div class="col" style="padding: 5px;">
              <label>{{$property['title']}}</label>
              <input type="text" name="propertyValues[{{$property['id']}}]" value="{{ $product['property_values'][$property['id']] ?? '' }}" class="form-control">
            </div>
            <div class="w-100"></div>
            @endforeach

          </div>
        </div>
      </div><!-- /.container-fluid -->
      <div class="col" style="padding: 5px;">
        <input type="submit" class="btn-btn-primary" value="Редактировать">
      </div>
    </form>
  </div>
</section>
<!-- /.content -->

@endsection